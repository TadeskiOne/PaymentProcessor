<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Rules;

use PaymentProcessor\Valuation\CommissionsFee\Entities\FeeAmountType;
use PaymentProcessor\Valuation\CommissionsFee\Entities\FeeOperationType;

class DepositRule extends AbstractRule implements CommissionFeeRuleInterface
{
    public function getAmount(): float
    {
        return 0.03;
    }

    public function getAmountType(): FeeAmountType
    {
        return FeeAmountType::percentage;
    }

    public function getOperationType(): FeeOperationType
    {
        return FeeOperationType::multiply;
    }
}
