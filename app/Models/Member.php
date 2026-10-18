<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    protected $guarded = ['id'];

    use SoftDeletes;

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function eventEdition()
    {
        return $this->belongsTo(EventEdition::class);
    }
}
