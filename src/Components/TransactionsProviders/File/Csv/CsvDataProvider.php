<?php

declare(strict_types=1);

namespace PaymentProcessor\Components\TransactionsProviders\File\Csv;

use PaymentProcessor\Components\TransactionsProviders\Definitions\DataProviderInterface;
use PaymentProcessor\Components\TransactionsProviders\File\InvalidSourceFileException;
use PaymentProcessor\Entities\Enums\InitiatorType;
use PaymentProcessor\Entities\Enums\TransactionType;
use PaymentProcessor\Entities\TransactionImmutableInterface;

final class CsvDataProvider implements DataProviderInterface
{
    private const COL_DATE = 0;
    private const COL_INITIATOR_ID = 1;
    private const COL_INITIATOR_TYPE = 2;
    private const COL_TYPE = 3;
    private const COL_AMOUNT = 4;
    private const COL_CURRENCY = 5;

    protected $file;
    protected array $currentLine;
    protected int $currentIndex;

    public function __construct(
        private readonly string $filePath,
        private readonly string $transactionClass,
        bool $skipHeader = true
    ) {
        $fileInfo = pathinfo($this->filePath);

        if (!is_readable($filePath) || $fileInfo['extension'] !== 'csv') {
            throw new InvalidSourceFileException($filePath);
        }

        $this->file = fopen($this->filePath, 'r');
        $this->currentIndex = $skipHeader ? 1 : 0;
    }

    public function rewind(): void
    {
        rewind($this->file);
        $this->currentIndex = 0;
        $this->currentLine = fgetcsv($this->file);
    }

    public function current(): TransactionImmutableInterface
    {
        return new ($this->transactionClass)(
            \DateTimeImmutable::createFromFormat('Y-m-d', $this->currentLine[self::COL_DATE]),
            (int) $this->currentLine[self::COL_INITIATOR_ID],
            constant(sprintf('%s::%s', InitiatorType::class, $this->currentLine[self::COL_INITIATOR_TYPE])),
            constant(sprintf('%s::%s', TransactionType::class, $this->currentLine[self::COL_TYPE])),
            (float) $this->currentLine[self::COL_AMOUNT],
            strtoupper((string) $this->currentLine[self::COL_CURRENCY])
        );
    }

    public function key(): int
    {
        return $this->currentIndex;
    }

    public function next(): void
    {
        $this->currentLine = fgetcsv($this->file) ?: [];
        ++$this->currentIndex;
    }

    public function valid(): bool
    {
        return !feof($this->file) && $this->currentLine !== false;
    }

    public function __destruct()
    {
        fclose($this->file);
    }
}
