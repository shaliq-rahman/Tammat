<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Image facade wrapping Intervention Image v3 with a v2-compatible API shim.
 *
 * @method static \App\Support\ImageWrapper make(mixed $source)
 * @method static \App\Support\ImageWrapper read(mixed $source)
 */
class Image extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'image';
    }
}
