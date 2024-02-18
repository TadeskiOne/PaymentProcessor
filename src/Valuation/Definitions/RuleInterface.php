<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\Definitions;

/**
 * Interface RuleInterface.
 *
 * This interface defines the methods for a rule that calculates fees based on certain criteria.
 */
interface RuleInterface
{
    /**
     * Retrieves the amount value of the calculation rule.
     *
     * @return float the amount value of the calculation rule
     */
    public function getAmount(): float;

    public function getAmountType(): RuleAmountTypeInterface;

    public function getOperationType(): RuleOperationTypeInterface;
}
