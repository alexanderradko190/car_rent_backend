<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReportWorker extends Command
{
    protected $signature = 'queue:report-worker';

    protected $description = 'Воркер для генерации отчета';

    public function handle(): void
    {
        $this->info('Воркер запущен');

        $this->call('queue:work', [
            '--queue' => 'go-report'
        ]);
    }
}
