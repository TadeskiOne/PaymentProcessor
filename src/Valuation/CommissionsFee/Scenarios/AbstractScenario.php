<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Scenarios;

use PaymentProcessor\Components\TransactionsProviders\Definitions\DataProviderInterface;
use PaymentProcessor\Valuation\Definitions\RulesCollectorInterface;
use PaymentProcessor\Valuation\Definitions\ScenarioInterface;

/**
 * The AbstractScenario class is an abstract class that implements the ScenarioInterface.
 * It serves as a base class for concrete scenario classes and provides some common functionality.
 */
abstract class AbstractScenario implements ScenarioInterface
{
    protected ?DataProviderInterface $dataProvider;

    public function __construct(protected readonly RulesCollectorInterface $collector)
    {
    }

    public function setDataProvider(?DataProviderInterface $dataProvider): void
    {
        $this->dataProvider = $dataProvider;
    }
}
