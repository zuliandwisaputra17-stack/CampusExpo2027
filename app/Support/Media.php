<?php

namespace App\Support;

class Media
{
    public static function url(?string $path, string $fallback = 'assets/school.webp'): string
    {
        return asset($path ? (str_starts_with($path, 'assets/') ? $path : 'storage/'.$path) : $fallback);
    }
}
