<?php

namespace App\Enums;

enum BooleanStatus: int
{
    case No = 0;
    case Yes = 1;

    public function toString(): string
    {
        return match ($this) {
            self::No => 'Нет',
            self::Yes => 'Да'
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::No => 'red',
            self::Yes => 'green',
        };
    }
}
