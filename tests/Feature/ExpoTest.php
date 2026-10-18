<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\EventEdition;
use App\Models\Member;
use App\Models\Schedule;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExpoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    private function campusData(): array
    {
        return ['name' => 'Universitas Test', 'slug' => 'universitas-test', 'abbreviation' => 'UT', 'city' => 'Jakarta', 'province' => 'DKI Jakarta', 'official_url' => 'https://example.org', 'display_order' => 0, 'is_active' => 1];
    }

    public function test_public_page_uses_active_event_and_excludes_hidden_records(): void
    {
        $campus = Campus::first();
        $campus->update(['is_active' => false]);
        $this->get('/')->assertOk()->assertSee('Campus Expo 2027')->assertSee('Masa depanmu')->assertDontSee('Aulia Rahma');
    }

    public function test_public_page_handles_no_active_event(): void
    {
        EventEdition::query()->update(['is_active' => false]);
        $this->get('/')->assertOk()->assertSee('Langkah baru, kemungkinan baru.');
    }

    public function test_guest_and_non_admin_cannot_access_cms(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/campuses')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_admin_login_and_logout(): void
    {
        $user = $this->admin();
        $this->post('/admin/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_wrong_password_and_public_user_are_rejected(): void
    {
        $user = User::factory()->create();
        $this->post('/admin/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_pages_and_all_forms_render(): void
    {
        $this->actingAs($this->admin())->get('/admin')->assertOk();
        foreach (config('cms') as $key => $definition) {
            $this->get('/admin/'.$key)->assertOk();
            $this->get('/admin/'.$key.'/create')->assertOk();
            $record = $definition['model']::first();
            if ($record) {
                $this->get('/admin/'.$key.'/'.$record->id.'/edit')->assertOk();
            }
        }
        $this->get('/admin/settings')->assertOk();
        $this->get('/admin/invalid')->assertNotFound();
    }

    public function test_campus_create_edit_and_soft_delete(): void
    {
        $this->actingAs($this->admin());
        $data = $this->campusData();
        $this->post('/admin/campuses', $data)->assertRedirect('/admin/campuses');
        $campus = Campus::where('slug', $data['slug'])->firstOrFail();
        $this->put('/admin/campuses/'.$campus->id, [...$data, 'name' => 'Universitas Baru'])->assertRedirect('/admin/campuses');
        $this->get('/')->assertSee('Universitas Baru');
        $this->delete('/admin/campuses/'.$campus->id)->assertRedirect();
        $this->assertSoftDeleted('campuses', ['id' => $campus->id]);
    }

    public function test_validation_blocks_unsafe_urls_and_duplicate_slugs(): void
    {
        $this->actingAs($this->admin());
        $data = $this->campusData();
        $this->post('/admin/campuses', [...$data, 'official_url' => 'javascript:alert(1)'])->assertSessionHasErrors('official_url');
        $this->post('/admin/campuses', [...$data, 'slug' => Campus::first()->slug])->assertSessionHasErrors('slug');
    }

    public function test_member_upload_is_optimized_and_visible_then_hidden(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());
        $data = ['name' => 'Test Anggota', 'slug' => 'test-anggota', 'event_edition_id' => EventEdition::first()->id, 'campus_id' => Campus::first()->id, 'study_program' => 'S1 Matematika', 'display_order' => 1, 'is_active' => 1, 'photo' => UploadedFile::fake()->image('portrait.png', 800, 1000)];
        $this->post('/admin/members', $data)->assertRedirect('/admin/members');
        $member = Member::where('slug', 'test-anggota')->firstOrFail();
        Storage::disk('public')->assertExists($member->photo);
        $this->assertStringEndsWith('.webp', $member->photo);
        $size = getimagesize(Storage::disk('public')->path($member->photo));
        $this->assertSame([400, 500], array_slice($size, 0, 2));
        $this->get('/')->assertSee('Test Anggota');
        unset($data['photo'],$data['is_active']);
        $this->put('/admin/members/'.$member->id, $data)->assertRedirect('/admin/members');
        $this->get('/')->assertDontSee('Test Anggota');
        $this->delete('/admin/members/'.$member->id)->assertRedirect();
        $this->assertSoftDeleted($member);
    }

    public function test_upload_rejects_non_image_and_oversized_image(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/campuses', [...$this->campusData(), 'cover_image' => UploadedFile::fake()->create('malicious.php', 1, 'application/x-php')])->assertSessionHasErrors('cover_image');
        $this->post('/admin/campuses', [...$this->campusData(), 'cover_image' => UploadedFile::fake()->image('large.jpg')->size(6000)])->assertSessionHasErrors('cover_image');
    }

    public function test_event_activation_switches_public_content(): void
    {
        $this->actingAs($this->admin());
        $old = EventEdition::first();
        $data = $old->only(['name', 'year', 'slug', 'event_start_at', 'event_end_at', 'venue_name', 'venue_address', 'description', 'hero_title', 'hero_description']);
        $data = [...$data, 'name' => 'Campus Expo 2028', 'year' => 2028, 'slug' => 'expo-2028', 'event_start_at' => '2028-01-18T08:00', 'event_end_at' => '2028-01-18T15:00', 'hero_title' => 'Bab Baru 2028', 'is_active' => 1];
        $this->post('/admin/event-editions', $data)->assertRedirect('/admin/event-editions');
        $this->assertSame(1, EventEdition::where('is_active', true)->count());
        $this->assertFalse($old->fresh()->is_active);
        $this->get('/')->assertSee('Bab Baru 2028')->assertDontSee('Aulia Rahma');
    }

    public function test_active_event_cannot_be_deleted(): void
    {
        $this->actingAs($this->admin())->delete('/admin/event-editions/'.EventEdition::first()->id)->assertSessionHasErrors('delete');
        $this->assertDatabaseCount('event_editions', 1);
    }

    public function test_schedule_crud_and_time_validation(): void
    {
        $this->actingAs($this->admin());
        $data = ['event_edition_id' => EventEdition::first()->id, 'event_date' => '2027-01-18', 'start_time' => '10:00', 'end_time' => '11:00', 'title' => 'Sesi Tes', 'location' => 'Aula', 'display_order' => 1, 'is_active' => 1];
        $this->post('/admin/schedules', [...$data, 'end_time' => '09:00'])->assertSessionHasErrors('end_time');
        $this->post('/admin/schedules', $data)->assertRedirect('/admin/schedules');
        $item = Schedule::where('title', 'Sesi Tes')->firstOrFail();
        $this->get('/')->assertSee('Sesi Tes');
        $this->put('/admin/schedules/'.$item->id, [...$data, 'title' => 'Sesi Diperbarui'])->assertRedirect('/admin/schedules');
        $this->delete('/admin/schedules/'.$item->id)->assertRedirect();
        $this->assertDatabaseMissing('schedules', ['id' => $item->id]);
    }

    public function test_settings_are_escaped_and_url_validation_is_enforced(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/settings', ['school_name' => '<script>alert(1)</script>'])->assertRedirect();
        $this->get('/')->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->post('/admin/settings', ['instagram' => 'javascript:alert(1)'])->assertSessionHasErrors('instagram');
    }

    public function test_faq_partner_gallery_crud(): void
    {
        $this->actingAs($this->admin());
        Storage::fake('public');
        foreach (['faqs' => ['question' => 'Pertanyaan tes?', 'answer' => 'Jawaban tes.'], 'partners' => ['name' => 'Partner Tes', 'type' => 'Sponsor', 'url' => 'https://example.org'], 'galleries' => ['image' => UploadedFile::fake()->image('gallery.jpg'), 'alt_text' => 'Foto tes', 'caption' => 'Dokumentasi tes']] as $resource => $data) {
            $this->post('/admin/'.$resource, [...$data, 'display_order' => 0, 'is_active' => 1])->assertRedirect('/admin/'.$resource);
            $model = config("cms.$resource.model");
            $item = $model::latest('id')->first();
            $this->delete('/admin/'.$resource.'/'.$item->id)->assertRedirect();
            $this->assertDatabaseMissing($item->getTable(), ['id' => $item->id]);
        }
    }

    public function test_password_reset_form_and_request(): void
    {
        $this->get('/admin/forgot-password')->assertOk();
        $this->get('/admin/reset-password/example-token?email=test@example.org')->assertOk();
        $this->post('/admin/forgot-password', ['email' => 'unknown@example.org'])->assertSessionHas('status');
    }

    public function test_event_countdown_states_at_boundaries(): void
    {
        $event = EventEdition::first();
        $this->travelTo($event->event_start_at->copy()->subSecond());
        $this->assertSame('upcoming', $event->state());
        $this->travelTo($event->event_start_at);
        $this->assertSame('live', $event->state());
        $this->travelTo($event->event_end_at);
        $this->assertSame('live', $event->state());
        $this->travelTo($event->event_end_at->copy()->addSecond());
        $this->assertSame('finished', $event->state());
        $this->travelBack();
    }

    public function test_seed_is_repeatable_and_relations_resolve(): void
    {
        $this->seed();
        $this->assertDatabaseCount('campuses', 6);
        $this->assertDatabaseCount('members', 8);
        $member = Member::first();
        $this->assertNotNull($member->campus);
        $this->assertNotNull($member->eventEdition);
        $this->assertSame(8, EventEdition::first()->members()->count());
    }

    public function test_schedule_reorder_updates_all_ids_and_rejects_incomplete_list(): void
    {
        $this->actingAs($this->admin());
        $event = EventEdition::first();
        $ids = $event->schedules()->orderBy('id')->pluck('id')->reverse()->values()->all();
        $this->get('/admin/schedules/reorder')->assertOk();
        $this->post('/admin/schedules/reorder', ['event_edition_id' => $event->id, 'ids' => $ids])->assertRedirect();
        $this->assertSame($ids, $event->schedules()->orderBy('display_order')->pluck('id')->all());
        $this->post('/admin/schedules/reorder', ['event_edition_id' => $event->id, 'ids' => [$ids[0]]])->assertUnprocessable();
    }

    public function test_school_logo_upload_and_map_validation(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());
        $this->post('/admin/settings', ['school_logo' => UploadedFile::fake()->image('logo.png', 200, 200)])->assertRedirect();
        $path = SiteSetting::where('key', 'school_logo')->value('value');
        Storage::disk('public')->assertExists($path);
        $this->get('/')->assertSee('storage/'.$path);
        $this->post('/admin/settings', ['maps_embed_url' => 'https://example.org/maps'])->assertSessionHasErrors('maps_embed_url');
        $this->post('/admin/settings', ['maps_embed_url' => 'https://www.google.com/maps/embed?pb=example'])->assertRedirect();
        $this->get('/')->assertSee('Peta lokasi Campus Expo');
    }

    public function test_setup_demo_command_preserves_existing_content(): void
    {
        SiteSetting::where('key', 'school_name')->update(['value' => 'Sekolah Saya']);
        $this->artisan('expo:seed-demo')->assertSuccessful();
        $this->assertDatabaseHas('site_settings', ['key' => 'school_name', 'value' => 'Sekolah Saya']);
        $this->assertDatabaseCount('members', 8);
    }

    public function test_security_headers_and_cms_status_filters(): void
    {
        $this->get('/')->assertHeader('X-Content-Type-Options', 'nosniff');
        $campus = Campus::first();
        $campus->update(['is_active' => false]);
        $this->actingAs($this->admin())->get('/admin/campuses?active=0&sort=name')->assertOk()->assertSee($campus->name)->assertDontSee('Institut Teknologi Bandung')->assertHeader('Cache-Control', 'no-store, private');
    }
}
