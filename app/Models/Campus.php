<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campus extends Model
{
    protected $guarded = ['id'];

    use SoftDeletes;

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
