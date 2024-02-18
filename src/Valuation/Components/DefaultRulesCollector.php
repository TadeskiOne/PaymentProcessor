<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\Components;

use PaymentProcessor\Valuation\Definitions\RuleInterface;
use PaymentProcessor\Valuation\Definitions\RulesCollectorInterface;
use Psr\Container\ContainerInterface;

class DefaultRulesCollector implements RulesCollectorInterface
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function collectRule(string $identifier): RuleInterface
    {
        return $this->container->get($identifier);
    }
}
