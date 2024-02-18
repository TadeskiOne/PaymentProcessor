<?php

declare(strict_types=1);

use DI\Container;
use PaymentProcessor\Components\CurrencyApi\Definitions\ApiInterface;
use PaymentProcessor\Components\CurrencyApi\ExactApi\SimpleApiProvider;
use PaymentProcessor\Services\ValuationService;
use PaymentProcessor\Valuation\CommissionsFee\Components\RulesMath;
use PaymentProcessor\Valuation\CommissionsFee\Components\DefaultTransactionsRegistry;
use PaymentProcessor\Valuation\CommissionsFee\Rules\SimpleFeeRule;
use PaymentProcessor\Valuation\CommissionsFee\Rules\HasFreeFromFeesRule;
use PaymentProcessor\Valuation\CommissionsFee\Scenarios\DepositScenario;
use PaymentProcessor\Valuation\CommissionsFee\Scenarios\WithdrawScenario;
use PaymentProcessor\Valuation\Components\DefaultRulesCollector;
use PaymentProcessor\Valuation\Definitions\RulesCollectorInterface;
use Psr\Container\ContainerInterface;

use PaymentProcessor\Valuation\CommissionsFee\Components\TransactionsRegistryInterface;
use function DI\autowire;
use function DI\create;
use function DI\factory;

return [
    ApiInterface::class                  => create(SimpleApiProvider::class),
    TransactionsRegistryInterface::class => factory(fn() => new DefaultTransactionsRegistry()),
    DefaultRulesCollector::class         => autowire()->constructorParameter('container', autowire(Container::class)),
    RulesMath::class                     => autowire(),
    HasFreeFromFeesRule::class           => fn (ContainerInterface $c) => new HasFreeFromFeesRule($c->get(RulesMath::class), $c->get(ApiInterface::class), $c->get(TransactionsRegistryInterface::class)),
    SimpleFeeRule::class                 => autowire(),
    RulesCollectorInterface::class       => fn (ContainerInterface $c) => new DefaultRulesCollector($c),
    DepositScenario::class               => fn (ContainerInterface $c) => new DepositScenario($c->get(RulesCollectorInterface::class)),
    WithdrawScenario::class              => fn (ContainerInterface $c) => new WithdrawScenario($c->get(RulesCollectorInterface::class)),
    ValuationService::class              => fn (ContainerInterface $c) => new ValuationService($c),
];
