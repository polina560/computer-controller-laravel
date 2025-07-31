<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ShellExecJob implements ShouldQueue
{
    use Queueable;

    public string $cmd;

    /**
     * Create a new job instance.
     */
    public function __construct(string $cmd)
    {
        $this->cmd = $cmd;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        exec($this->cmd);
    }
}
