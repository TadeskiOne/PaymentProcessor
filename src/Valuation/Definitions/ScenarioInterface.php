<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\Definitions;

use PaymentProcessor\Components\TransactionsProviders\Definitions\DataProviderInterface;
use PaymentProcessor\Entities\TransactionImmutableInterface;

/**
 * Interface ScenarioInterface.
 *
 * This interface represents a scenario that can be applied to a transaction.
 * Implementing classes should provide functionality to set a data provider and apply the scenario to a transaction.
 */
interface ScenarioInterface
{
    public function setDataProvider(DataProviderInterface $dataProvider): void;

    public function applyTo(TransactionImmutableInterface $transaction): mixed;
}
