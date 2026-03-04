<?php

namespace App\Services\Report;

use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportStorage
{
    public function __construct(
        private readonly FilesystemFactory $filesystems
    ) {
        //
    }

    private function diskName(): string
    {
        return config('reports.disk', env('REPORTS_DISK', 'reports'));
    }

    public function exists(string $path): bool
    {
        return $this->filesystems->disk($this->diskName())->exists($path);
    }

    public function download(string $path): StreamedResponse
    {
        return $this->filesystems->disk($this->diskName())->download($path);
    }
}
