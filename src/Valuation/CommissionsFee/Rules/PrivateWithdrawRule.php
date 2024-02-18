<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Rules;

use PaymentProcessor\Components\CurrencyApi\Definitions\ApiInterface;
use PaymentProcessor\Entities\TransactionImmutableInterface;
use PaymentProcessor\Valuation\CommissionsFee\Components\CurrencyConverterTrait;
use PaymentProcessor\Valuation\CommissionsFee\Components\RulesMath;
use PaymentProcessor\Valuation\CommissionsFee\Components\TransactionsRegistryInterface;
use PaymentProcessor\Valuation\CommissionsFee\Entities\FeeAmountType;
use PaymentProcessor\Valuation\CommissionsFee\Entities\FeeOperationType;

class PrivateWithdrawRule extends AbstractRule implements CommissionFeeRuleInterface
{
    use CurrencyConverterTrait;

    private const WEEKLY_AMOUNT_RESTRICTION = 1000; // in base currency;
    private const FREE_FROM_FEES_TRANSACTIONS_COUNT = 3;

    public function __construct(
        RulesMath $operations,
        ApiInterface $currenciesApi,
        private readonly TransactionsRegistryInterface $transactionsRegistry
    ) {
        $this->rates = $currenciesApi->getRates();

        parent::__construct($operations);
    }

    public function getAmount(): float
    {
        return 0.3;
    }

    public function getAmountType(): FeeAmountType
    {
        return FeeAmountType::percentage;
    }

    public function getOperationType(): FeeOperationType
    {
        return FeeOperationType::multiply;
    }

    public function applyTo(TransactionImmutableInterface $transaction): TransactionImmutableInterface
    {
        $convertedAmount = $this->convertToBaseCurrency($transaction);

        $this->validateTransaction($transaction);
        $this->transactionsRegistry->defineInitiator($transaction);
        $this->transactionsRegistry->defineWeekPeriod($transaction);

        if ($this->transactionsRegistry->hasWeeklyRegistry()) {
            $commissionFee = 0.00;

            if ($this->transactionsRegistry->getWeeklyRegistry()->count() < self::FREE_FROM_FEES_TRANSACTIONS_COUNT) {
                $amountByWeek = 0.00;

                /** @var TransactionImmutableInterface $prevTransaction */
                foreach ($this->transactionsRegistry->getWeeklyRegistry() as $prevTransaction) {
                    $amountByWeek += $this->convertToBaseCurrency($prevTransaction);
                }

                if ($amountByWeek > self::WEEKLY_AMOUNT_RESTRICTION) {
                    $commissionFee = $this->calcFeeForExceededAmountOfWeeklyRestriction($convertedAmount, $transaction);
                } else {
                    $amountByWeek += $convertedAmount;
                    if ($amountByWeek > self::WEEKLY_AMOUNT_RESTRICTION) {
                        $commissionFee = $this->calcFeeForExceededAmountOfWeeklyRestriction($amountByWeek - self::WEEKLY_AMOUNT_RESTRICTION, $transaction);
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
            amount: $convertedAmount > self::WEEKLY_AMOUNT_RESTRICTION
                       ? $this->calcFeeForExceededAmountOfWeeklyRestriction(
                           $convertedAmount - self::WEEKLY_AMOUNT_RESTRICTION,
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
