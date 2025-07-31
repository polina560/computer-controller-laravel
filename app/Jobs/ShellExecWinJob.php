<?php

namespace App\Jobs;

use Illuminate\Foundation\Queue\Queueable;

class ShellExecWinJob {

    use Queueable;

    public string $cmd;
    public string $cmd2;

    /**
     * Create a new job instance.
     */
    public function __construct(string $cmd, string $cmd2)
    {
        $this->cmd = $cmd;
        $this->cmd2 = $cmd2;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        exec($this->cmd);
        exec($this->cmd2);
    }
}
