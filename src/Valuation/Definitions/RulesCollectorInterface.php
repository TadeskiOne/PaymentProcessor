<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\Definitions;

/**
 * Interface RulesCollectorInterface.
 *
 * This interface defines the methods to collect rules based on their identifier.
 */
interface RulesCollectorInterface
{
    public function collectRule(string $identifier): RuleInterface;
}
