<?php

namespace App\Jobs;

use App\Models\Computer;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ShellExecUpdateJob implements ShouldQueue
{
    use Queueable;

    public string $cmd;
    public int $computerId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $cmd, int $computerId)
    {
        $this->computerId = $computerId;
        $this->cmd = $cmd;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        exec($this->cmd, $output);
        $computer = Computer::findOne($this->computerId);
        if (!$computer) {
            throw new Exception('Computer not found');
        }
        $computer->statusUpdate($output);
    }
}
