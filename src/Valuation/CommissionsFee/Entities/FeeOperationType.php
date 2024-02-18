<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Entities;

use PaymentProcessor\Valuation\Definitions\RuleOperationTypeInterface;

enum FeeOperationType: int implements RuleOperationTypeInterface
{
    case multiply = 0;
    case divide = 1;
    case add = 2;
    case reduce = 3;
}
