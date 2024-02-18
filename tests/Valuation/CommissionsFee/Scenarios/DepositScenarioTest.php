<?php

declare(strict_types=1);

namespace PaymentProcessor\Tests\Valuation\CommissionsFee\Scenarios;

use PaymentProcessor\Entities\Enums\InitiatorType;
use PaymentProcessor\Entities\Enums\TransactionType;
use PaymentProcessor\Entities\TransactionImmutable;
use PaymentProcessor\Valuation\CommissionsFee\Components\RulesMath;
use PaymentProcessor\Valuation\CommissionsFee\Exceptions\NegativeAmountException;
use PaymentProcessor\Valuation\CommissionsFee\Exceptions\ZeroAmountException;
use PaymentProcessor\Valuation\CommissionsFee\Rules\DepositRule;
use PaymentProcessor\Valuation\CommissionsFee\Scenarios\DepositScenario;
use PaymentProcessor\Valuation\Definitions\RulesCollectorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use PaymentProcessor\Tests\Mocks\CurrencyApi\SetUpCurrencyApi;

class DepositScenarioTest extends TestCase
{
    use ProphecyTrait;
    use SetUpCurrencyApi;
    private DepositScenario $scenario;

    public function setUp(): void
    {
        $collector = $this->prophesize(RulesCollectorInterface::class);
        $collector->collectRule(DepositRule::class)->willReturn(new DepositRule(new RulesMath()));

        $this->scenario = new DepositScenario($collector->reveal());
    }

    public function transactionsProvider(): array
    {
        return [
            'Three transactions, different types' => [
                [
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2014-12-31'),
                        4,
                        InitiatorType::private,
                        TransactionType::deposit,
                        1200.00,
                        'EUR'
                    ),
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2015-01-01'),
                        4,
                        InitiatorType::private,
                        TransactionType::deposit,
                        1000.00,
                        'EUR'
                    ),
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2016-01-05'),
                        4,
                        InitiatorType::private,
                        TransactionType::withdraw,
                        1200.00,
                        'EUR'
                    ),
                ],
                [0.36, 0.3, null],
            ],
            'Three transactions, different types, different initiators' => [
                [
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2014-12-31'),
                        4,
                        InitiatorType::private,
                        TransactionType::deposit,
                        1200.00,
                        'EUR'
                    ),
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2015-01-01'),
                        4,
                        InitiatorType::private,
                        TransactionType::withdraw,
                        1200.00,
                        'EUR'
                    ),
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2016-01-05'),
                        1,
                        InitiatorType::private,
                        TransactionType::withdraw,
                        1200.00,
                        'EUR'
                    ),
                ],
                [0.36, null, null],
            ],
            'Three transactions, different types, different initiators, different initiator type' => [
                [
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2014-12-31'),
                        4,
                        InitiatorType::private,
                        TransactionType::deposit,
                        1200.00,
                        'EUR'
                    ),
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2015-01-01'),
                        2,
                        InitiatorType::business,
                        TransactionType::withdraw,
                        1000.00,
                        'EUR'
                    ),
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2016-01-05'),
                        1,
                        InitiatorType::private,
                        TransactionType::deposit,
                        1000.00,
                        'EUR'
                    ),
                ],
                [0.36, null, 0.3],
            ],
        ];
    }

    /**
     * @dataProvider transactionsProvider
     */
    public function testApplyTo($transactions, $expectedResult): void
    {
        foreach ($transactions as $i => $transaction) {
            $result = $this->scenario->applyTo($transaction);
            $this->assertEquals($expectedResult[$i], $result?->getAmount());
        }
    }

    public function invalidTransactionsProvider(): array
    {
        return [
            'Three transactions, one with zero, one with unknown currency' => [
                [
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2014-12-31'),
                        4,
                        InitiatorType::private,
                        TransactionType::deposit,
                        1200.00,
                        'UNKNOWN'
                    ),
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2015-01-01'),
                        1,
                        InitiatorType::business,
                        TransactionType::deposit,
                        0.00,
                        'EUR'
                    ),
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2016-01-05'),
                        5,
                        InitiatorType::private,
                        TransactionType::withdraw,
                        1000.00,
                        'EUR'
                    ),
                ],
                ZeroAmountException::class,
            ],

            'Two invalid transaction' => [
                [
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2014-12-31'),
                        4,
                        InitiatorType::private,
                        TransactionType::deposit,
                        1200.00,
                        'UNKNOWN'
                    ),
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2015-01-01'),
                        2,
                        InitiatorType::business,
                        TransactionType::deposit,
                        0.00,
                        'EUR'
                    ),
                ],
                ZeroAmountException::class,
            ],

            'Two transaction, one with negative amount' => [
                [
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2014-12-31'),
                        5,
                        InitiatorType::business,
                        TransactionType::deposit,
                        1200.00,
                        'JPY'
                    ),
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2015-01-01'),
                        5,
                        InitiatorType::business,
                        TransactionType::deposit,
                        -1000.00,
                        'EUR'
                    ),
                ],
                NegativeAmountException::class,
            ],
        ];
    }

    /**
     * @dataProvider invalidTransactionsProvider
     */
    public function testInvalidApplyTo($transactions, $expectedException): void
    {
        $this->expectException($expectedException);

        foreach ($transactions as $i => $transaction) {
            $this->scenario->applyTo($transaction);
        }
    }
}
