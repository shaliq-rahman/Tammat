<?php

namespace App\Support;

class DotenvEditor
{
    protected string $path;
    protected array $lines = [];
    protected bool $loaded = false;

    public function __construct()
    {
        $this->path = base_path('.env');
    }

    protected function load(): void
    {
        if ($this->loaded) return;
        $this->lines = file_exists($this->path) ? file($this->path, FILE_IGNORE_NEW_LINES) : [];
        $this->loaded = true;
    }

    public function keyExists(string $key): bool
    {
        $this->load();
        foreach ($this->lines as $line) {
            if (str_starts_with(trim($line), $key . '=')) return true;
        }
        return false;
    }

    public function getValue(string $key): ?string
    {
        $this->load();
        foreach ($this->lines as $line) {
            if (str_starts_with(trim($line), $key . '=')) {
                $value = substr(trim($line), strlen($key) + 1);
                return trim($value, '"\'');
            }
        }
        return null;
    }

    public function setKey(string $key, string $value): static
    {
        $this->load();
        $value = str_contains($value, ' ') ? '"' . $value . '"' : $value;
        $newLine = $key . '=' . $value;
        foreach ($this->lines as $i => $line) {
            if (str_starts_with(trim($line), $key . '=')) {
                $this->lines[$i] = $newLine;
                return $this;
            }
        }
        $this->lines[] = $newLine;
        return $this;
    }

    public function addEmpty(): static
    {
        $this->load();
        $this->lines[] = '';
        return $this;
    }

    public function save(): static
    {
        file_put_contents($this->path, implode("\n", $this->lines) . "\n");
        return $this;
    }

    public function deleteBackups(): static
    {
        foreach (glob(base_path('.env.backup.*')) as $file) {
            @unlink($file);
        }
        return $this;
    }
}
