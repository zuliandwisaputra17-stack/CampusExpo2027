<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\EventEdition;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Member;
use App\Models\Partner;
use App\Models\Schedule;
use App\Models\SiteSetting;

class HomeController extends Controller
{
    public function __invoke()
    {
        $event = EventEdition::where('is_active', true)->first();
        $campuses = Campus::where('is_active', true)->withCount(['members' => fn ($q) => $q->where('is_active', true)->where('event_edition_id', $event?->id)])->orderBy('display_order')->get();
        $members = Member::with('campus')->where('is_active', true)->where('event_edition_id', $event?->id)->whereHas('campus', fn ($q) => $q->where('is_active', true))->orderBy('display_order')->get();
        $schedules = Schedule::where('event_edition_id', $event?->id)->where('is_active', true)->orderBy('event_date')->orderBy('display_order')->orderBy('start_time')->get();
        $related = fn ($model) => $model::where('is_active', true)->where(fn ($q) => $q->whereNull('event_edition_id')->orWhere('event_edition_id', $event?->id))->orderBy('display_order')->get();

        return view('home', ['event' => $event, 'campuses' => $campuses, 'members' => $members, 'schedules' => $schedules, 'galleries' => $related(Gallery::class), 'faqs' => $related(Faq::class), 'partners' => $related(Partner::class), 'settings' => SiteSetting::pluck('value', 'key')->all()]);
    }
}
