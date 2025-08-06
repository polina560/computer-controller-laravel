<?php

namespace App\Jobs;

use App\Models\Computer;
use App\Models\ComputerLog;
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
        $this->cmd = $cmd;
        $this->computerId = $computerId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        exec($this->cmd, $output);

        $computer = Computer::where('id', $this->computerId)->first();
        if (!$computer) {
            throw new Exception('Computer not found');
        }

        $computer->statusUpdate($output);

        $computer_log = new ComputerLog;
        $computer_log->computer_id = $computer->id;
        $computer_log->status = $computer->status;
        $computer_log->updated_at = time();

        $computer_log->save();
    }
}
