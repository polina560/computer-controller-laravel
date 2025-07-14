<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

/**
 * This is the model class for table "{{%texts}}".
 *
 * @author  b.kataev <buvaysar2032@gmail.com>
 *
 * @property int $id [int] ID
 * @property string $key [varchar(255)] Ключ текстового поля
 * @property string $value Значение текстового поля
 */
#[Schema(properties: [
    new Property(property: 'key', type: 'string'),
    new Property(property: 'value', type: 'string'),
])]
class Text extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];
}
