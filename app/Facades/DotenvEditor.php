<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class DotenvEditor extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Support\DotenvEditor::class;
    }
}
