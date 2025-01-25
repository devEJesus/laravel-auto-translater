<?php

namespace LaravelAutoTranslator\Storage;

use Illuminate\Support\Facades\File;

class BaseDirectory
{
    protected string $directoryPath;

    public function __construct(string $directoryPath)
    {
        $this->directoryPath = $directoryPath;
    }

    public function getPath(?string $path = null): string
    {
        return $this->directoryPath.'/'.$path;
    }

    public function exists(?string $path = null, bool $create = false): bool
    {
        $isDirectory = File::isDirectory($this->directoryPath.'/'.$path);

        return $isDirectory;
    }

    public function create(?string $path = null): void
    {
        $fullPath = $this->directoryPath.'/'.$path;

        if (! File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
        }
    }

    /**
     * Get files from a directory
     *
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public function getFiles(string $path): array
    {
        return File::files($this->directoryPath.'/'.$path);
    }
}
