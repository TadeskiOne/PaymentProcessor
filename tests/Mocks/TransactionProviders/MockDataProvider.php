<?php

declare(strict_types=1);

namespace PaymentProcessor\Tests\Mocks\TransactionProviders;

use PaymentProcessor\Components\TransactionsProviders\Definitions\DataProviderInterface;
use PaymentProcessor\Entities\Enums\InitiatorType;
use PaymentProcessor\Entities\Enums\TransactionType;
use PaymentProcessor\Entities\TransactionImmutable;
use PaymentProcessor\Entities\TransactionImmutableInterface;
use Prophecy\PhpUnit\ProphecyTrait;

class MockDataProvider implements DataProviderInterface
{
    use ProphecyTrait;
    private array $data = [];
    private int $position = 0;

    public function __construct()
    {
        $this->data = json_decode(file_get_contents(__DIR__.'/mock_transactions.json'), true);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): TransactionImmutableInterface
    {
        $line = $this->data[$this->position];

        return new TransactionImmutable(
            \DateTimeImmutable::createFromFormat('Y-m-d', $line['date']),
            (int) $line['id'],
            constant(sprintf('%s::%s', InitiatorType::class, $line['type'])),
            constant(sprintf('%s::%s', TransactionType::class, $line['operation'])),
            (float) $line['amount'],
            strtoupper((string) $line['currency'])
        );
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->data[$this->position]);
    }
}
