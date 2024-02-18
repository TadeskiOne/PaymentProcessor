<?php

declare(strict_types=1);

namespace PaymentProcessor\Entities;

use PaymentProcessor\Entities\Enums\InitiatorType;
use PaymentProcessor\Entities\Enums\TransactionType;

interface TransactionImmutableInterface
{
    public function getDateTime(): \DateTimeImmutable;

    public function getInitiatorId(): int;

    public function getInitiatorType(): InitiatorType;

    public function getType(): TransactionType;

    public function getAmount(): float;

    public function getCurrencyTitle(): string;

    public function modify(
        ?\DateTimeImmutable $dateTime = null,
        ?int $initiatorId = null,
        ?InitiatorType $initiatorType = null,
        ?TransactionType $type = null,
        ?float $amount = null,
        ?string $currencyTitle = null
    ): TransactionImmutable;

    public function __toString(): string;
}
