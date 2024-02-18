<?php

declare(strict_types=1);

namespace PaymentProcessor\Entities\Enums;

enum InitiatorType: int implements StringableEnumInterface
{
    case private = 0;
    case business = 1;

    public function toString(): string
    {
        return match ($this) {
            self::private => 'private',
            self::business => 'business',
        };
    }
}
