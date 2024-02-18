<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Rules;

use PaymentProcessor\Components\CurrencyApi\Definitions\ApiInterface;
use PaymentProcessor\Entities\TransactionImmutableInterface;
use PaymentProcessor\Valuation\CommissionsFee\Components\CurrencyConverterTrait;
use PaymentProcessor\Valuation\CommissionsFee\Components\RulesMath;
use PaymentProcessor\Valuation\CommissionsFee\Components\TransactionsRegistryInterface;

class HasFreeFromFeesRule extends AbstractRule implements CommissionFeeRuleInterface
{
    use CurrencyConverterTrait;
    private ?int $weeklyAmountRestriction = null;
    private ?int $freeFromFeesCount = 0;

    public function __construct(
        RulesMath $operations,
        ApiInterface $currenciesApi,
        private readonly TransactionsRegistryInterface $transactionsRegistry
    ) {
        $this->rates = $currenciesApi->getRates();

        parent::__construct($operations);
    }

    public function getOptionsNames(): array
    {
        return [
            'weeklyAmountRestriction',
            'freeFromFeesCount',
        ];
    }

    public function setOptions(...$options): static
    {
        foreach ($options as $name => $value) {
            if (property_exists($this, $name)) {
                $this->{$name} = $value;
            }
        }

        return $this;
    }

    public function applyTo(TransactionImmutableInterface $transaction): TransactionImmutableInterface
    {
        $convertedAmount = $this->convertToBaseCurrency($transaction);

        $this->validateTransaction($transaction);
        $this->transactionsRegistry->defineInitiator($transaction);
        $this->transactionsRegistry->defineWeekPeriod($transaction);

        if ($this->transactionsRegistry->hasWeeklyRegistry()) {
            if ($this->transactionsRegistry->getWeeklyRegistry()->count() < $this->freeFromFeesCount && !$this->weeklyAmountRestriction) {
                $commissionFee = 0.00;
            } elseif ($this->transactionsRegistry->getWeeklyRegistry()->count() < $this->freeFromFeesCount) {
                $commissionFee = $amountByWeek = 0.00;

                /** @var TransactionImmutableInterface $prevTransaction */
                foreach ($this->transactionsRegistry->getWeeklyRegistry() as $prevTransaction) {
                    $amountByWeek += $this->convertToBaseCurrency($prevTransaction);
                }

                if ($amountByWeek > $this->weeklyAmountRestriction) {
                    $commissionFee = $this->calcFeeForExceededAmountOfWeeklyRestriction($convertedAmount, $transaction);
                } else {
                    $amountByWeek += $convertedAmount;
                    if ($amountByWeek > $this->weeklyAmountRestriction) {
                        $commissionFee = $this->calcFeeForExceededAmountOfWeeklyRestriction($amountByWeek - $this->weeklyAmountRestriction, $transaction);
                    }
                }
            } else {
                $commissionFee = $this->operations->ceil($this->operations->applyOperation($transaction->getAmount(), $this));
            }

            $this->transactionsRegistry->addWeeklyTransaction($transaction);

            return $transaction->modify(amount: $commissionFee);
        }

        $this->transactionsRegistry->addWeeklyTransaction($transaction);

        return $transaction->modify(
            amount: $this->weeklyAmountRestriction && $convertedAmount > $this->weeklyAmountRestriction
                       ? $this->calcFeeForExceededAmountOfWeeklyRestriction(
                           $convertedAmount - $this->weeklyAmountRestriction,
                           $transaction
                       )
                       : 0.00
        );
    }

    private function calcFeeForExceededAmountOfWeeklyRestriction(
        float $exceededAmount,
        TransactionImmutableInterface $transaction
    ): float {
        return $this->operations->ceil(
            $this->operations->applyOperation(
                $this->convertToOriginalCurrency($exceededAmount, $transaction),
                $this
            )
        );
    }
}
