<?php

namespace App\Support;

use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

/**
 * Compatibility shim wrapping Intervention Image v3 with a v2-like API.
 */
class ImageWrapper
{
    protected ImageInterface $image;

    public function __construct(ImageInterface $image)
    {
        $this->image = $image;
    }

    /**
     * v2 compat: Image::make($source)
     */
    public static function make(mixed $source): self
    {
        $driver = config('image.driver', 'gd') === 'imagick'
            ? new \Intervention\Image\Drivers\Imagick\Driver()
            : new GdDriver();

        $manager = new ImageManager($driver);
        $image = $manager->read($source);

        return new self($image);
    }

    /**
     * v3 style: Image::read($source)
     */
    public static function read(mixed $source): self
    {
        return self::make($source);
    }

    /**
     * v2 compat: orientate() — auto-rotate based on EXIF
     * In v3 this is handled via the Imagick driver automatically or can be applied.
     */
    public function orientate(): self
    {
        // v3 GD driver handles orientation via EXIF automatically on read.
        // This is a no-op shim for v2 compatibility.
        return $this;
    }

    /**
     * v2 compat: resize($width, $height, callable $callback = null)
     *
     * In v2, the callback accepted $constraint->aspectRatio() and $constraint->upsize().
     * In v3 we map these to the appropriate v3 methods.
     */
    public function resize(?int $width, ?int $height, ?callable $callback = null): self
    {
        if ($callback !== null) {
            // Parse what the callback would do by running it with a fake constraint
            $constraint = new class {
                public bool $aspectRatio = false;
                public bool $upsize = false;
                public function aspectRatio(): void { $this->aspectRatio = true; }
                public function upsize(): void { $this->upsize = true; }
            };
            $callback($constraint);

            if ($constraint->aspectRatio && $constraint->upsize) {
                // Scale down only, maintaining aspect ratio
                $this->image = $this->image->scaleDown($width, $height);
            } elseif ($constraint->aspectRatio) {
                // Scale (both up and down) maintaining aspect ratio
                $this->image = $this->image->scale($width, $height);
            } else {
                // Hard resize without maintaining aspect ratio
                $this->image = $this->image->resize($width, $height);
            }
        } else {
            $this->image = $this->image->resize($width, $height);
        }

        return $this;
    }

    /**
     * v2 compat: fit($width, $height)
     * In v3, this is cover()
     */
    public function fit(int $width, int $height): self
    {
        $this->image = $this->image->cover($width, $height);
        return $this;
    }

    /**
     * v2 compat: ->encode($format, $quality)
     * Returns self for chaining; stores format/quality for stream/save.
     */
    public function encode(?string $format = null, int $quality = 90): self
    {
        if ($format !== null) {
            $this->image = $this->image->encodeByExtension($format, quality: $quality);
        }
        return $this;
    }

    /**
     * v2 compat: ->stream() — returns the binary image data
     */
    public function stream(): string
    {
        if ($this->image instanceof \Intervention\Image\Interfaces\EncodedImageInterface) {
            return (string) $this->image;
        }
        return (string) $this->image->encodeByExtension('jpg', quality: 90);
    }

    /**
     * Allow calling v3 methods directly on the underlying image.
     */
    public function __call(string $name, array $args): mixed
    {
        $result = $this->image->$name(...$args);

        if ($result instanceof ImageInterface) {
            $this->image = $result;
            return $this;
        }

        return $result;
    }

    /**
     * Cast to string (returns binary image data).
     */
    public function __toString(): string
    {
        return $this->stream();
    }
}
