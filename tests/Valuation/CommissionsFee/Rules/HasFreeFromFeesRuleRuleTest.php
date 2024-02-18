<?php

declare(strict_types=1);

namespace PaymentProcessor\Tests\Valuation\CommissionsFee\Rules;

use PaymentProcessor\Entities\Enums\InitiatorType;
use PaymentProcessor\Entities\Enums\TransactionType;
use PaymentProcessor\Entities\TransactionImmutable;
use PaymentProcessor\Tests\Mocks\CurrencyApi\SetUpCurrencyApi;
use PaymentProcessor\Valuation\CommissionsFee\Components\RulesMath;
use PaymentProcessor\Valuation\CommissionsFee\Components\DefaultTransactionsRegistry;
use PaymentProcessor\Valuation\CommissionsFee\Entities\FeeAmountType;
use PaymentProcessor\Valuation\CommissionsFee\Entities\FeeOperationType;
use PaymentProcessor\Valuation\CommissionsFee\Exceptions\NegativeAmountException;
use PaymentProcessor\Valuation\CommissionsFee\Exceptions\UndefinedCurrencyException;
use PaymentProcessor\Valuation\CommissionsFee\Exceptions\ZeroAmountException;
use PaymentProcessor\Valuation\CommissionsFee\Rules\HasFreeFromFeesRule;
use PHPUnit\Framework\TestCase;

class HasFreeFromFeesRuleRuleTest extends TestCase
{
    use SetUpCurrencyApi;

    private HasFreeFromFeesRule $rule;

    public function setUp(): void
    {
        $apiInterface = $this->setUpApi();
        $this->rule = (new HasFreeFromFeesRule(new RulesMath(), $apiInterface->reveal(), new DefaultTransactionsRegistry()))
            ->setAmount(0.3)
            ->setAmountType(FeeAmountType::percentage)
            ->setOperationType(FeeOperationType::multiply)
            ->setOptions(weeklyAmountRestriction: 1000, freeFromFeesCount: 3);
    }

    public function transactionsProvider(): array
    {
        return [
            'Three transactions' => [
                [
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2014-12-31'),
                        4,
                        InitiatorType::private,
                        TransactionType::withdraw,
                        1200.00,
                        'EUR'
                    ),
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2015-01-01'),
                        4,
                        InitiatorType::private,
                        TransactionType::withdraw,
                        1000.00,
                        'EUR'
                    ),
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2016-01-05'),
                        4,
                        InitiatorType::private,
                        TransactionType::withdraw,
                        1000.00,
                        'EUR'
                    ),
                ],
                [0.6, 3, 0],
            ],
        ];
    }

    /**
     * @dataProvider transactionsProvider
     */
    public function testApplyTo($transactions, $expectedResult): void
    {
        foreach ($transactions as $i => $transaction) {
            $result = $this->rule->applyTo($transaction);
            $this->assertEquals($expectedResult[$i], $result->getAmount());
        }
    }

    public function invalidTransactionsProvider(): array
    {
        return [
            // Test invalid currency.
            'Invalid currency' => [
                new TransactionImmutable(
                    \DateTimeImmutable::createFromFormat('Y-m-d', '2014-12-31'),
                    4,
                    InitiatorType::private,
                    TransactionType::withdraw,
                    1200.00,
                    'INVALID'
                ),
                UndefinedCurrencyException::class,
            ],
            // Test transaction with 0 amount.
            'Zero transaction' => [
                new TransactionImmutable(
                    \DateTimeImmutable::createFromFormat('Y-m-d', '2014-11-11'),
                    4,
                    InitiatorType::private,
                    TransactionType::withdraw,
                    0.00,
                    'EUR'
                ),
                ZeroAmountException::class,
            ],
            // Test transaction with negative amount
            'Negative transaction value' => [
                new TransactionImmutable(
                    \DateTimeImmutable::createFromFormat('Y-m-d', '2014-11-11'),
                    4,
                    InitiatorType::private,
                    TransactionType::withdraw,
                    -100.00,
                    'EUR'
                ),
                NegativeAmountException::class,
            ],
        ];
    }

    /**
     * @dataProvider invalidTransactionsProvider
     */
    public function testInvalidApplyTo($transaction, $expectedException): void
    {
        $this->expectException($expectedException);
        $this->rule->applyTo($transaction);
    }
}
