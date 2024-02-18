<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Entities;

use PaymentProcessor\Valuation\Definitions\RuleAmountTypeInterface;

enum FeeAmountType: int implements RuleAmountTypeInterface
{
    case percentage = 0;
    case amount = 1;
}
