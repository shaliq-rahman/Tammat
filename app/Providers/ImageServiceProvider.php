<?php

namespace App\Providers;

use App\Support\ImageWrapper;
use Illuminate\Support\ServiceProvider;

class ImageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('image', function ($app) {
            return new class {
                public function make(mixed $source): ImageWrapper
                {
                    return ImageWrapper::make($source);
                }

                public function read(mixed $source): ImageWrapper
                {
                    return ImageWrapper::read($source);
                }
            };
        });
    }
}
