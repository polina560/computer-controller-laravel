<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BooleanStatus;
use common\components\queue\ShellExecUpdateJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        $this->status = 1;
        $this->save();
    }

    public function powerOff()
    {
        $this->status = 0;
        $this->save();
    }

    public function powerOnList()
    {
        $this->status = 1;
        $this->save();
    }

    public function powerOffList()
    {
        $this->status = 0;
        $this->save();
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
        }
        //        } else {
        //            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        //                Yii::$app->queue->push(new ShellExecUpdateJob([
        //                    'cmd' => "ping -n 1 {$this->ip_address}",
        //                    'computerId' => $this->id,
        //                ]));
        //            } //Windows
        //            else {
        //                $job = new ShellExecUpdateJob([
        //                    'cmd' => "ping -c1 {$this->ip_address}",
        //                    'computerId' => $this->id,
        //                ]);
        //                Yii::$app->queue->push($job);
        //            } //*nix
        //        }

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
}
