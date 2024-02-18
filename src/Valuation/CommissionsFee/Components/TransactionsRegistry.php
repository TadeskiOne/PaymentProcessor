<?php

declare(strict_types=1);

namespace PaymentProcessor\Valuation\CommissionsFee\Components;

use PaymentProcessor\Entities\TransactionImmutableInterface;

class TransactionsRegistry
{
    /* date_from|date_to */
    private ?string $weekPeriod = null;
    private ?int $initiatorId = null;
    private readonly \ArrayObject $registry;

    public function __construct()
    {
        $this->registry = new \ArrayObject([]);
    }

    public function defineInitiator(TransactionImmutableInterface $transaction): self
    {
        $this->initiatorId = $transaction->getInitiatorId();
        $this->setInitiatorRegistry();

        return $this;
    }

    public function defineWeekPeriod(TransactionImmutableInterface $transaction): self
    {
        $start = $transaction->getDateTime()->modify('Monday this week');
        $end = $start->modify('+6 days');
        $this->weekPeriod = sprintf('%s|%s', $start->format('Y-m-d'), $end->format('Y-m-d'));

        return $this;
    }

    public function hasWeeklyRegistry(): bool
    {
        if ($this->initiatorId !== null && $this->weekPeriod !== null) {
            return isset($this->registry[$this->initiatorId]['week'][$this->weekPeriod]);
        }

        return false;
    }

    public function getWeeklyRegistry(): \ArrayObject
    {
        return $this->registry[$this->initiatorId]['week'][$this->weekPeriod];
    }

    public function addWeeklyTransaction(TransactionImmutableInterface $transaction): void
    {
        if ($this->initiatorId === null || $this->weekPeriod === null) {
            return;
        }

        if (!isset($this->registry[$this->initiatorId]['week'])) {
            $this->registry[$this->initiatorId]->offsetSet('week', new \ArrayObject([]));
        }

        if (isset($this->registry[$this->initiatorId]['week'][$this->weekPeriod])) {
            $this->registry[$this->initiatorId]['week'][$this->weekPeriod]->append($transaction);
        } else {
            $this->registry[$this->initiatorId]['week']->offsetSet($this->weekPeriod, new \ArrayObject([$transaction]));
        }
    }

    private function setInitiatorRegistry(): void
    {
        if ($this->initiatorId && !isset($this->registry[$this->initiatorId])) {
            $this->registry->offsetSet($this->initiatorId, new \ArrayObject([]));
        }
    }
}
