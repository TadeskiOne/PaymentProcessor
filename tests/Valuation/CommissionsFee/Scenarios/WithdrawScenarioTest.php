<?php

declare(strict_types=1);

namespace PaymentProcessor\Tests\Valuation\CommissionsFee\Scenarios;

use PaymentProcessor\Entities\Enums\InitiatorType;
use PaymentProcessor\Entities\Enums\TransactionType;
use PaymentProcessor\Entities\TransactionImmutable;
use PaymentProcessor\Valuation\CommissionsFee\Components\RulesMath;
use PaymentProcessor\Valuation\CommissionsFee\Components\DefaultTransactionsRegistry;
use PaymentProcessor\Valuation\CommissionsFee\Exceptions\NegativeAmountException;
use PaymentProcessor\Valuation\CommissionsFee\Exceptions\UndefinedCurrencyException;
use PaymentProcessor\Valuation\CommissionsFee\Exceptions\ZeroAmountException;
use PaymentProcessor\Valuation\CommissionsFee\Rules\HasFreeFromFeesRule;
use PaymentProcessor\Valuation\CommissionsFee\Rules\SimpleFeeRule;
use PaymentProcessor\Valuation\CommissionsFee\Scenarios\WithdrawScenario;
use PaymentProcessor\Valuation\Definitions\RulesCollectorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use PaymentProcessor\Tests\Mocks\CurrencyApi\SetUpCurrencyApi;

class WithdrawScenarioTest extends TestCase
{
    use ProphecyTrait;
    use SetUpCurrencyApi;
    private WithdrawScenario $scenario;

    public function setUp(): void
    {
        $math = new RulesMath();
        $privateWithdrawRule = new HasFreeFromFeesRule($math, $this->setUpApi()->reveal(), new DefaultTransactionsRegistry());
        $businessWithdrawRule = new SimpleFeeRule($math);

        $collector = $this->prophesize(RulesCollectorInterface::class);
        $collector->collectRule(HasFreeFromFeesRule::class)->willReturn($privateWithdrawRule);
        $collector->collectRule(SimpleFeeRule::class)->willReturn($businessWithdrawRule);

        $this->scenario = new WithdrawScenario($collector->reveal());
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
                        TransactionType::withdraw,
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
                [null, 0, 0.6],
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
                [null, 0.6, 0.6],
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
                        TransactionType::withdraw,
                        1000.00,
                        'EUR'
                    ),
                ],
                [null, 5, 0],
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
            'Three transactions, one with zero' => [
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
                        0.00,
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
                ZeroAmountException::class,
            ],

            'Two invalid transaction' => [
                [
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2014-12-31'),
                        4,
                        InitiatorType::private,
                        TransactionType::withdraw,
                        1200.00,
                        'UNKNOWN'
                    ),
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2015-01-01'),
                        4,
                        InitiatorType::private,
                        TransactionType::withdraw,
                        0.00,
                        'EUR'
                    ),
                ],
                UndefinedCurrencyException::class,
            ],

            'Two transaction, one with negative amount' => [
                [
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2014-12-31'),
                        4,
                        InitiatorType::private,
                        TransactionType::withdraw,
                        1200.00,
                        'JPY'
                    ),
                    new TransactionImmutable(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2015-01-01'),
                        4,
                        InitiatorType::private,
                        TransactionType::withdraw,
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
