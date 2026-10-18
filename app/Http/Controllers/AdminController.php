<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\EventEdition;
use App\Models\Gallery;
use App\Models\Member;
use App\Models\Schedule;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    private function resource(string $resource): array
    {
        abort_unless(array_key_exists($resource, config('cms')), 404);

        return config("cms.$resource");
    }

    public function dashboard()
    {
        return view('admin.dashboard', ['counts' => ['Kampus' => Campus::count(), 'Anggota' => Member::count(), 'Jadwal' => Schedule::count(), 'Galeri' => Gallery::count()], 'event' => EventEdition::where('is_active', true)->first()]);
    }

    public function index(Request $r, string $resource)
    {
        $definition = $this->resource($resource);
        $model = $definition['model'];
        $q = $model::query();
        $searchField = isset($definition['fields']['name']) ? 'name' : (isset($definition['fields']['title']) ? 'title' : (isset($definition['fields']['question']) ? 'question' : 'caption'));
        if ($r->filled('active') && in_array($r->input('active'), ['0', '1'], true)) {
            $q->where('is_active', $r->boolean('active'));
        }
        if (isset($definition['fields']['event_edition_id']) && $r->filled('edition')) {
            $q->where('event_edition_id', (int) $r->input('edition'));
        }
        if ($r->filled('q')) {
            $q->where($searchField, 'like', '%'.$r->string('q').'%');
        }

        if ($r->input('sort') === 'name') {
            $q->orderBy($searchField);
        } elseif ($resource !== 'event-editions' && $r->input('sort') === 'order') {
            $q->orderBy('display_order');
        }

        return view('admin.index', compact('resource', 'definition') + ['items' => $q->orderBy('id', 'desc')->paginate(15)->withQueryString(), 'searchField' => $searchField, 'events' => EventEdition::orderByDesc('year')->get()]);
    }

    public function form(string $resource, ?int $id = null)
    {
        $definition = $this->resource($resource);
        $model = $definition['model'];

        return view('admin.form', compact('resource', 'definition') + ['item' => $id ? $model::findOrFail($id) : new $model, 'events' => EventEdition::orderByDesc('year')->get(), 'campuses' => Campus::orderBy('name')->get()]);
    }

    public function save(Request $r, string $resource, ?int $id = null)
    {
        $definition = $this->resource($resource);
        $model = $definition['model'];
        $item = $id ? $model::findOrFail($id) : new $model;
        $rules = [];
        foreach ($definition['fields'] as $field => $spec) {
            $rules[$field] = explode('|', $spec['rules']);
            if ($spec['type'] === 'file') {
                $rules[$field] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=6000,max_height=6000'];
            }
            if ($field === 'slug' && $resource !== 'members') {
                $rules[$field][] = Rule::unique($item->getTable(), 'slug')->ignore($item->id);
            }
        }
        if ($resource === 'galleries' && ! $item->exists) {
            $rules['image'][0] = 'required';
        }
        if ($resource === 'members') {
            $rules['campus_id'] = ['required', Rule::exists('campuses', 'id')->whereNull('deleted_at')];
        }
        $data = $r->validate($rules);
        $newFiles = [];
        foreach ($definition['fields'] as $field => $spec) {
            if ($spec['type'] === 'checkbox') {
                $data[$field] = $r->boolean($field);
            }
            if ($spec['type'] === 'file') {
                unset($data[$field]);
                if ($r->hasFile($field)) {
                    $data[$field] = $this->upload($r->file($field), $resource === 'members');
                    $newFiles[] = $data[$field];
                }
            }
        }
        try {
            DB::transaction(function () use ($resource, $data, $item) {
                if ($resource === 'event-editions' && ($data['is_active'] ?? false)) {
                    EventEdition::query()->lockForUpdate()->get();
                    EventEdition::where('is_active', true)->update(['is_active' => false]);
                }
                $item->fill($data)->save();
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($newFiles);
            throw $e;
        }

        return redirect('/admin/'.$resource)->with('status', 'Perubahan berhasil disimpan.');
    }

    private function upload($file, bool $portrait = false): string
    {
        // Re-encode validated images, strip metadata, and constrain rendered dimensions.
        $image = imagecreatefromstring(file_get_contents($file->getRealPath()));
        abort_unless($image, 422);
        $w = imagesx($image);
        $h = imagesy($image);
        $targetW = $portrait ? 400 : min(1400, $w);
        $targetH = $portrait ? 500 : (int) round($h * $targetW / $w);
        $out = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($out, false);
        imagesavealpha($out, true);
        if ($portrait) {
            $cropW = min($w, $h * 4 / 5);
            $cropH = $cropW * 5 / 4;
            imagecopyresampled($out, $image, 0, 0, (int) (($w - $cropW) / 2), (int) (($h - $cropH) / 2), $targetW, $targetH, (int) $cropW, (int) $cropH);
        } else {
            imagecopyresampled($out, $image, 0, 0, 0, 0, $targetW, $targetH, $w, $h);
        }
        ob_start();
        imagewebp($out, null, 84);
        $bytes = ob_get_clean();
        $path = 'uploads/'.Str::uuid().'.webp';
        abort_unless(Storage::disk('public')->put($path, $bytes), 500, 'Gagal menyimpan gambar.');
        imagedestroy($image);
        imagedestroy($out);

        return $path;
    }

    public function destroy(string $resource, int $id)
    {
        $definition = $this->resource($resource);
        $item = $definition['model']::findOrFail($id);
        if ($resource === 'event-editions' && $item->is_active) {
            return back()->withErrors(['delete' => 'Aktifkan edisi lain sebelum menghapus acara aktif.']);
        }
        if ($resource === 'event-editions' && $item->members()->withTrashed()->exists()) {
            return back()->withErrors(['delete' => 'Edisi ini memiliki anggota. Nonaktifkan edisi untuk menyimpan arsipnya.']);
        }
        $item->delete();

        return back()->with('status', 'Data berhasil dihapus.');
    }

    public function reorderForm(Request $request)
    {
        $events = EventEdition::orderByDesc('year')->get();
        $event = $request->filled('edition') ? $events->firstWhere('id', (int) $request->input('edition')) : ($events->firstWhere('is_active', true) ?? $events->first());
        $schedules = $event ? $event->schedules()->orderBy('event_date')->orderBy('display_order')->orderBy('start_time')->get() : collect();

        return view('admin.reorder', compact('events', 'event', 'schedules'));
    }

    public function reorder(Request $request)
    {
        $data = $request->validate(['event_edition_id' => 'required|exists:event_editions,id', 'ids' => 'required|array', 'ids.*' => 'required|integer|distinct']);
        DB::transaction(function () use ($data) {
            $existing = Schedule::where('event_edition_id', $data['event_edition_id'])->lockForUpdate()->pluck('id')->sort()->values()->all();
            $submitted = collect($data['ids'])->map(fn ($id) => (int) $id)->sort()->values()->all();
            abort_unless($existing === $submitted, 422, 'Daftar jadwal berubah. Muat ulang sebelum menyimpan.');
            foreach ($data['ids'] as $order => $id) {
                Schedule::whereKey($id)->update(['display_order' => $order]);
            }
        });

        return back()->with('status', 'Urutan jadwal berhasil disimpan.');
    }

    public function settings()
    {
        return view('admin.settings', ['settings' => SiteSetting::pluck('value', 'key')->all()]);
    }

    public static function settingsFields(): array
    {
        return ['school_name' => 'Nama sekolah', 'school_logo' => 'Logo sekolah', 'favicon' => 'Favicon', 'maps_embed_url' => 'URL embed Google Maps (opsional)', 'contact_email' => 'Email panitia', 'whatsapp' => 'Nomor WhatsApp (62...)', 'instagram' => 'URL Instagram', 'tiktok' => 'URL TikTok', 'youtube' => 'URL YouTube', 'maps_url' => 'URL Google Maps', 'seo_title' => 'Judul SEO', 'seo_description' => 'Deskripsi SEO', 'stat_campuses' => 'Statistik kampus', 'stat_members' => 'Statistik anggota', 'stat_programs' => 'Statistik program studi', 'stat_visitors' => 'Statistik pengunjung', 'parking_info' => 'Informasi parkir'];
    }

    public function saveSettings(Request $r)
    {
        $rules = [];
        foreach (self::settingsFields() as $key => $label) {
            $rule = 'nullable|string|max:2000';
            if (in_array($key, ['instagram', 'tiktok', 'youtube', 'maps_url'])) {
                $rule = 'nullable|url:http,https|max:2000';
            }
            if ($key === 'contact_email') {
                $rule = 'nullable|email|max:255';
            }
            if ($key === 'whatsapp') {
                $rule = 'nullable|regex:/^[0-9]{8,16}$/';
            }
            if (in_array($key, ['school_logo', 'favicon'])) {
                $rules[$key] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024', 'dimensions:max_width=2048,max_height=2048'];

                continue;
            }
            if ($key === 'maps_embed_url') {
                $rules[$key] = ['nullable', 'url:https', 'max:2000', function ($attribute, $value, $fail) {
                    $host = parse_url($value, PHP_URL_HOST);
                    $path = parse_url($value, PHP_URL_PATH) ?? '';
                    if (! in_array($host, ['www.google.com', 'maps.google.com'], true) || ! str_starts_with($path, '/maps')) {
                        $fail('Gunakan URL embed HTTPS dari Google Maps.');
                    }
                }];

                continue;
            }
            $rules[$key] = $rule;
        }
        $data = $r->validate($rules);
        foreach ($data as $key => $value) {
            if (in_array($key, ['school_logo', 'favicon'])) {
                if (! $r->hasFile($key)) {
                    continue;
                }
                $value = $this->upload($r->file($key));
            }
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('status', 'Pengaturan website diperbarui.');
    }
}
