<?php

declare(strict_types=1);

namespace PaymentProcessor\Tests\Valuation\CommissionsFee\Rules;

use PaymentProcessor\Entities\Enums\InitiatorType;
use PaymentProcessor\Entities\Enums\TransactionType;
use PaymentProcessor\Entities\TransactionImmutable;
use PaymentProcessor\Valuation\CommissionsFee\Components\RulesMath;
use PaymentProcessor\Valuation\CommissionsFee\Exceptions\NegativeAmountException;
use PaymentProcessor\Valuation\CommissionsFee\Exceptions\ZeroAmountException;
use PaymentProcessor\Valuation\CommissionsFee\Rules\DepositRule;
use PHPUnit\Framework\TestCase;

class DepositRuleTest extends TestCase
{
    private DepositRule $rule;

    public function setUp(): void
    {
        $this->rule = new DepositRule(new RulesMath());
    }

    public function transactionsProvider(): array
    {
        return [
            'Two transactions' => [
                [
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2016-01-05'),
                        1,
                        InitiatorType::private,
                        TransactionType::deposit,
                        200.00,
                        'EUR'
                    ),
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2016-01-10'),
                        1,
                        InitiatorType::business,
                        TransactionType::deposit,
                        10000.00,
                        'JPY'
                    ),
                ],
                [0.06, 3],
            ],
            'Two transactions, different initiators' => [
                [
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2016-01-05'),
                        1,
                        InitiatorType::private,
                        TransactionType::deposit,
                        200.00,
                        'EUR'
                    ),
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2016-01-10'),
                        2,
                        InitiatorType::business,
                        TransactionType::deposit,
                        10000.00,
                        'JPY'
                    ),
                ],
                [0.06, 3],
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
            // Test transaction with 0 amount.
            'Zero transaction' => [
                new TransactionImmutable(
                    \DateTimeImmutable::createFromFormat('Y-m-d', '2014-11-11'),
                    4,
                    InitiatorType::business,
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
                    InitiatorType::business,
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
