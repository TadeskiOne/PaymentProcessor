<?php

declare(strict_types=1);

namespace PaymentProcessor\Tests\Valuation;

use PaymentProcessor\Services\ValuationService;
use PaymentProcessor\Valuation\CommissionsFee\Components\RulesMath;
use PaymentProcessor\Valuation\CommissionsFee\Components\DefaultTransactionsRegistry;
use PaymentProcessor\Valuation\CommissionsFee\Rules\SimpleFeeRule;
use PaymentProcessor\Valuation\CommissionsFee\Rules\HasFreeFromFeesRule;
use PaymentProcessor\Valuation\CommissionsFee\Scenarios\DepositScenario;
use PaymentProcessor\Valuation\CommissionsFee\Scenarios\WithdrawScenario;
use PaymentProcessor\Valuation\Definitions\RulesCollectorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use PaymentProcessor\Tests\Mocks\CurrencyApi\SetUpCurrencyApi;
use PaymentProcessor\Tests\Mocks\TransactionProviders\MockDataProvider;

class ValuationServiceTest extends TestCase
{
    use ProphecyTrait;
    use SetUpCurrencyApi;

    private ValuationService $service;

    protected function setUp(): void
    {
        $math = new RulesMath();

        $collector = $this->prophesize(RulesCollectorInterface::class);
        $collector->collectRule(HasFreeFromFeesRule::class)
                  ->willReturn(
                      new HasFreeFromFeesRule(
                          $math,
                          $this->setUpApi()->reveal(),
                          new DefaultTransactionsRegistry()
                      )
                  );

        $collector->collectRule(SimpleFeeRule::class)
                  ->willReturn(new SimpleFeeRule($math));

        $container = $this->prophesize(ContainerInterface::class);
        $container->get(DepositScenario::class)
                  ->willReturn(new DepositScenario($collector->reveal()));
        $container->get(WithdrawScenario::class)
                  ->willReturn(new WithdrawScenario($collector->reveal()));

        $this->service = new ValuationService($container->reveal());
    }

    public function testCalculateCommissionFee()
    {
        // Act
        $commissionFee = $this->service->calculateCommissionFees(new MockDataProvider());
        $expected = [0.60, 3.00, 0.00, 0.06, 1.50, 0, 0.70, 0.30, 0.30, 3.00, 0.00, 0.00, 8611.41];

        // Assert
        foreach ($commissionFee as $i => $fee) {
            $this->assertEquals($expected[$i], $fee);
        }
    }
}
