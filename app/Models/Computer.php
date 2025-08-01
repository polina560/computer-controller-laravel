<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BooleanStatus;
use App\Jobs\ShellExecJob;
use App\Jobs\ShellExecUpdateJob;
use App\Jobs\ShellExecWinJob;
use App\Jobs\WakeOnLANJob;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(properties: [
    new Property(property: 'computer_name', type: 'string'),
    new Property(property: 'full_name', type: 'string'),
    new Property(property: 'ip_address', type: 'string'),
    new Property(property: 'mac_address', type: 'string'),
    new Property(property: 'status', type: 'string'),
])]
class Computer extends Model
{
    protected $table = 'computer';

    protected $fillable = [
        'computer_name',
        'full_name',
        'ip_address',
        'mac_address',
        'status',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function powerOn()
    {
        if (!isset($this->mac_address)) {
            throw new \Exception('No mac');
        }
        WakeOnLANJob::dispatch($this->id);

    }

    /**
     * Выключение компьютера в локальной сети
     */
    public function powerOff(): void
    {
        $user = config('computer.RC_USER');
        $pass = config('computer.RC_PASS');

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            //            exec("net use \\\\$this->ip_address $pass /user:$user");
            //            exec("Shutdown /s /f -m \\\\$this->ip_address"); // Windows
            ShellExecWinJob::dispatch("net use \\\\$this->ip_address $pass /user:$user", "Shutdown /s /f -m \\\\$this->ip_address");
        } else {
            //            exec("net rpc shutdown -I $this->ip_address -U $user%$pass -f -t 0");
            ShellExecJob::dispatch("net rpc shutdown -I $this->ip_address -U $user%$pass -f -t 0");

        }
    }

    public function ping($one = null)
    {
        if ($one) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec("ping -n 1 {$this->ip_address}", $output);
            } // Windows
            else {
                exec("ping -c1 {$this->ip_address}", $output);
            } // *nix
            $this->statusUpdate($output);
        } else {
            $command = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
                ? "ping -n 1 {$this->ip_address}"
                : "ping -c1 {$this->ip_address}";

            ShellExecUpdateJob::dispatch($command, $this->id);
        }

        $computer_log = new ComputerLog;
        $computer_log->computer_id = $this->id;
        $computer_log->status = $this->status;
        $computer_log->updated_at = time();

        $computer_log->save();

    }

    public function statusUpdate(array $output): void
    {
        if (preg_match("/ TTL=\d+/i", implode("\n", $output))) {
            $this->status = BooleanStatus::Yes->value;
        } else {
            $this->status = BooleanStatus::No->value;
        }
        $this->save();
    }

    public static function _privateWakeOnLan(string $addr, array $addr_byte, int $socket_number = 7): array
    {
        $hw_addr = '';

        for ($a = 0; $a < 6; $a++) {
            $hw_addr .= chr(hexdec($addr_byte[$a]));
        }

        $msg = chr(255).chr(255).chr(255).chr(255).chr(255).chr(255);

        for ($a = 1; $a <= 16; $a++) {
            $msg .= $hw_addr;
        }

        if (!extension_loaded('sockets')) {
            throw new Exception(
                'Error: Extension <strong>php_sockets</strong> is not loaded! You need to enable it in <strong>php.ini</strong>',
            );
        }
        $s = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if (!$s) {
            throw new Exception(
                'Can\'t create socket!\n'.
                'Error: \''.socket_last_error($s).'\' - '.socket_strerror(socket_last_error($s)),
            );
        } else {
            $opt_ret = socket_set_option($s, SOL_SOCKET, SO_BROADCAST, true);

            if ($opt_ret < 0) {
                throw new Exception(
                    'setsockopt() failed, error: '.socket_strerror((int) $opt_ret),
                );
            }

            if (socket_sendto($s, $msg, strlen($msg), 0, $addr, $socket_number)) {
                $content = bin2hex($msg);
                socket_close($s);

                return [
                    'message' => "Magic Packet Sent!<BR>\n".
                        'Data: <textarea readonly rows="1" name="content" cols="'.strlen(
                            $content,
                        ).'">'.$content."</textarea><BR>\n".
                        'Port: '.$socket_number."<br>\n".
                        'MAC: '.implode(':', $addr_byte)."<BR>\n",
                ];
            } else {
                throw new Exception(
                    'Magic Packet failed to send!',
                );
            }
        }
    }
}
