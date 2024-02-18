<?php

declare(strict_types=1);

namespace PaymentProcessor\Entities\Enums;

enum TransactionType: int implements StringableEnumInterface
{
    case deposit = 0;
    case withdraw = 1;

    public function toString(): string
    {
        return match ($this) {
            self::deposit => 'deposit',
            self::withdraw => 'withdraw',
        };
    }
}
