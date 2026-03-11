<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendMailWorker extends Command
{
    protected $signature = 'queue:mail-worker';

    protected $description = 'Воркер для отправки email уведомлений';

    public function handle(): void
    {
        $this->info('Mail worker запущен');

        $this->call('queue:work', [
            '--queue' => 'send-mail'
        ]);
    }
}
