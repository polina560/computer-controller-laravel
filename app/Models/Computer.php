<?php

declare(strict_types=1);

namespace App\Models;

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


}
