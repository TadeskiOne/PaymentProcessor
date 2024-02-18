<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Rules;

use /*
 * Interface ApiInterface
 *
 * This interface defines the methods that should be implemented by any Currency API class.
 * It provides functionality to retrieve currency rates, convert currency amounts, and get the list of supported currencies.
 *
 * @package PaymentProcessor\Components\CurrencyApi\Definitions
 */
PaymentProcessor\Components\CurrencyApi\Definitions\ApiInterface;
use /*
 * Interface RatesCollectionInterface
 *
 * This interface specifies the methods that a rates collection class should implement.
 * Rates collection represents a set of currency exchange rates for a specific point in time.
 * It provides methods for adding and retrieving rates.
 *
 * @package PaymentProcessor\Components\CurrencyApi\Definitions
 */
PaymentProcessor\Components\CurrencyApi\Definitions\RatesCollectionInterface;
use /*
 * Interface TransactionImmutableInterface
 *
 * This interface defines the contract for an immutable transaction object.
 * A transaction represents a financial transaction and provides read-only access
 * to its properties.
 */
PaymentProcessor\Entities\TransactionImmutableInterface;
use /*
 * Class RulesMath
 *
 * This class provides various mathematical operations and rules for calculating commissions fees.
 */
PaymentProcessor\Valuation\CommissionsFee\Components\RulesMath;
use /*
 * Class FeeAmountType
 *
 * Represents the types of fee amounts for commissions fees.
 */
PaymentProcessor\Valuation\CommissionsFee\Entities\FeeAmountType;
use /*
 * Class FeeOperationType
 *
 * Represents the different types of fee operations.
 */
PaymentProcessor\Valuation\CommissionsFee\Entities\FeeOperationType;
use /*
 * Exception class for handling undefined currency errors in the CommissionsFee class.
 *
 * This exception is thrown when an undefined currency is encountered
 * when calculating commissions fees.
 *
 * @package PaymentProcessor\Valuation\CommissionsFee\Exceptions
 */
PaymentProcessor\Valuation\CommissionsFee\Exceptions\UndefinedCurrencyException;

class PrivateWithdrawRule extends AbstractRule implements CommissionFeeRuleInterface
{
    private const WEEKLY_AMOUNT_RESTRICTION = 1000; // in base currency;
    private const FREE_FROM_FEES_TRANSACTIONS_COUNT = 3;

    private readonly \ArrayObject $transactionsRegistry;
    private readonly RatesCollectionInterface $rates;

    public function __construct(
        RulesMath $operations,
        private readonly ApiInterface $currenciesApi
    ) {
        $this->transactionsRegistry = new \ArrayObject([]);
        $this->rates = $this->currenciesApi->getRates();

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
        $this->validateTransaction($transaction);

        $convertedAmount = $this->convertToBaseCurrency($transaction);
        $start = $transaction->getDateTime()->modify('Monday this week');
        $end = $start->modify('+6 days');
        $transactionInterval = sprintf('%s|%s', $start->format('Y-m-d'), $end->format('Y-m-d'));

        unset($start, $end);

        if (!isset($this->transactionsRegistry[$transaction->getInitiatorId()])) {
            $this->transactionsRegistry->offsetSet($transaction->getInitiatorId(), new \ArrayObject([]));
        }

        if (isset($this->transactionsRegistry[$transaction->getInitiatorId()][$transactionInterval])) {
            $commissionFee = 0.00;

            if ($this->transactionsRegistry[$transaction->getInitiatorId()][$transactionInterval]->count() < self::FREE_FROM_FEES_TRANSACTIONS_COUNT) {
                $amountByWeek = 0.00;

                /** @var TransactionImmutableInterface $prevTransaction */
                foreach (
                    $this->transactionsRegistry[$transaction->getInitiatorId()][$transactionInterval] as $prevTransaction
                ) {
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

            $this->transactionsRegistry[$transaction->getInitiatorId()][$transactionInterval]->append($transaction);

            return $transaction->modify(amount: $commissionFee);
        }

        $this->transactionsRegistry[$transaction->getInitiatorId()]->offsetSet(
            $transactionInterval,
            new \ArrayObject([$transaction])
        );

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

    // region Currency conversion
    private function convertToBaseCurrency(TransactionImmutableInterface $transaction): float
    {
        return $transaction->getAmount() / $this->getCurrencyRate($transaction);
    }

    private function convertToOriginalCurrency(float $amount, TransactionImmutableInterface $transaction): float
    {
        return $amount * $this->getCurrencyRate($transaction);
    }

    private function getCurrencyRate(TransactionImmutableInterface $transaction)
    {
        if ($this->rates->getBaseRate()->getCurrencyTitle() === $transaction->getCurrencyTitle()) {
            return $this->rates->getBaseRate()->getExchangeValue();
        }

        if (!isset($this->rates->getRates()[$transaction->getCurrencyTitle()])) {
            throw new UndefinedCurrencyException($transaction);
        }

        return $this->rates->getRates()[$transaction->getCurrencyTitle()];
    }
    // endregion
}
