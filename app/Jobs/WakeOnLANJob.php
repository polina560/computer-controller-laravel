<?php

namespace App\Jobs;

use App\Models\Computer;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WakeOnLANJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $computerId;

    public function __construct(int $computerId)
    {
        $this->computerId = $computerId;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $computer = Computer::find($this->computerId);
        if ($computer === null) {
            throw new Exception('No such computer');
        }

        $mac_reg = '/^([0-9A-F]{2}).([0-9A-F]{2}).([0-9A-F]{2}).([0-9A-F]{2}).([0-9A-F]{2}).([0-9A-F]{2})$/i';
        if (!isset($computer->mac_address)) {
            throw new Exception('No mac');
        }
        if (!preg_match($mac_reg, $computer->mac_address, $addr_byte)) {
            throw new Exception('Wrong MAC format');
        }

        array_shift($addr_byte);
        $result = $computer->_privateWakeOnLan('192.168.50.255', $addr_byte);

        if ($result != null) {
            $computer->ping();
        } else {
            throw new Exception('$result is null');
        }
    }
}
