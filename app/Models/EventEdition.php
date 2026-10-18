<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventEdition extends Model
{
    protected $guarded = ['id'];

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function faqs()
    {
        return $this->hasMany(Faq::class);
    }

    public function partners()
    {
        return $this->hasMany(Partner::class);
    }

    protected function casts(): array
    {
        return ['event_start_at' => 'datetime', 'event_end_at' => 'datetime', 'is_active' => 'boolean'];
    }

    public function state(): string
    {
        return now()->lt($this->event_start_at) ? 'upcoming' : (now()->lte($this->event_end_at) ? 'live' : 'finished');
    }
}
