<?php

declare(strict_types=1);

namespace PaymentProcessor\Services;

use PaymentProcessor\Components\TransactionsProviders\Definitions\DataProviderInterface;
use PaymentProcessor\Entities\TransactionImmutableInterface;
use PaymentProcessor\Valuation\CommissionsFee\Scenarios\DepositScenario;
use PaymentProcessor\Valuation\CommissionsFee\Scenarios\WithdrawScenario;
use PaymentProcessor\Valuation\Definitions\ScenarioInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ValuationService
 *s
 * This class is responsible for calculating commission fees for transactions using different scenarios.
 */
final class ValuationService
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function calculateCommissionFees(DataProviderInterface $dataProvider): \ArrayAccess
    {
        $fees = new \ArrayObject([]);

        $scenariosByPriority = [
            DepositScenario::class,
            WithdrawScenario::class,
        ];

        foreach ($dataProvider as $transaction) {
            foreach ($scenariosByPriority as $scenarioIdentifier) {
                try {
                    /**
                     * @var ScenarioInterface             $scenario
                     * @var TransactionImmutableInterface $feeTransaction
                     */
                    $scenario = $this->container->get($scenarioIdentifier);
                    $feeTransaction = $scenario->applyTo($transaction);

                    if ($feeTransaction === null) {
                        continue;
                    }

                    $fees->append($feeTransaction->getAmount());
                } catch (NotFoundExceptionInterface $e) {
                    continue;
                }
            }

            unset($rules);
        }

        return $fees;
    }
}
