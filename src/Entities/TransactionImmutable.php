<?php

declare(strict_types=1);

namespace PaymentProcessor\Entities;

use PaymentProcessor\Entities\Enums\InitiatorType;
use PaymentProcessor\Entities\Enums\TransactionType;

class TransactionImmutable implements TransactionImmutableInterface
{
    public function __construct(
        protected readonly \DateTimeImmutable $dateTime,
        protected readonly int $initiatorId = 0,
        protected readonly InitiatorType $initiatorType = InitiatorType::private,
        protected readonly TransactionType $type = TransactionType::deposit,
        protected readonly float $amount = 0.00,
        protected readonly string $currencyTitle = 'EUR',
    ) {
    }

    public function getDateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function getInitiatorId(): int
    {
        return $this->initiatorId;
    }

    public function getInitiatorType(): InitiatorType
    {
        return $this->initiatorType;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrencyTitle(): string
    {
        return strtoupper($this->currencyTitle);
    }

    public function modify(
        ?\DateTimeImmutable $dateTime = null,
        ?int $initiatorId = null,
        ?InitiatorType $initiatorType = null,
        ?TransactionType $type = null,
        ?float $amount = null,
        ?string $currencyTitle = null
    ): TransactionImmutable {
        return new static(
            $dateTime ?? $this->dateTime,
            $initiatorId ?? $this->initiatorId,
            $initiatorType ?? $this->initiatorType,
            $type ?? $this->type,
            $amount ?? $this->amount,
            $currencyTitle ?? $this->currencyTitle,
        );
    }

    public function __toString(): string
    {
        return implode(
            ', ',
            [
                $this->dateTime->format('Y-m-d'),
                $this->initiatorId,
                $this->initiatorType->toString(),
                $this->type->toString(),
                $this->amount,
                $this->currencyTitle,
            ]
        );
    }
}
