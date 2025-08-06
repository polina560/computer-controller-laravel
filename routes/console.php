<?php

use App\Models\Computer;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('computers:ping', function () {
    $this->info('Starting to ping all computers...');

    $total = Computer::count();
    $success = 0;

    Computer::query()->chunkById(100, function ($computers) use (&$success) {
        foreach ($computers as $computer) {
            try {
                $computer->ping();
                $success++;
                $this->info("Pinged computer {$computer->id} ({$computer->computer_name})");
            } catch (Exception $e) {
                $this->error("Failed to ping computer {$computer->id}: ".$e->getMessage());
            }
        }
    });

    $this->info("Completed! Successfully pinged {$success} of {$total} computers.");
})->purpose('Ping all computers');

Artisan::command('computers:clear-logs', function (): void {
    Computer::clearLog();
});


Schedule::command('telescope:prune')->daily();
Schedule::command('backup:clean')->daily()->at('01:00');
Schedule::command('backup:run')->daily()->at('01:30');
Schedule::command('computers:ping')->everyMinute();
Schedule::command('computers:clear-logs')->daily()->at('01:00');
