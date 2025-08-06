<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComputerLog extends Model
{
    protected $table = 'computer_log';

    protected $fillable = [
        'computer_id',
        'status',
    ];

    public function computer(): BelongsTo
    {
        return $this->belongsTo(Computer::class, 'computer_id');
    }
}
