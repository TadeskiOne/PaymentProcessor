<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\Definitions;

interface RuleInterface
{
    public function setAmount(float $amount): static;

    public function getAmount(): float;

    public function setAmountType(RuleAmountTypeInterface $amountType): static;

    public function getAmountType(): RuleAmountTypeInterface;

    public function setOperationType(RuleOperationTypeInterface $feeOperationType): static;

    public function getOperationType(): RuleOperationTypeInterface;

    public function getOptionsNames(): array;

    public function setOptions(mixed ...$options): static;
}
