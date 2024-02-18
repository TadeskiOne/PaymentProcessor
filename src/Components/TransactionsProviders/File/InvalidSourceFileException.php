<?php

declare(strict_types=1);

namespace PaymentProcessor\Components\TransactionsProviders\File;

/**
 * Class InvalidImportFileException.
 */
final class InvalidSourceFileException extends \LogicException
{
    protected $message = 'Invalid source file';
    protected $code = 1000;
    private string $filePath;

    public function __construct(string $filePath, ?\Throwable $previous = null)
    {
        parent::__construct($this->message, $this->code, $previous);
        $this->filePath = $filePath;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
