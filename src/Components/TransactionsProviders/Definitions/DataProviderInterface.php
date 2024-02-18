<?php

declare(strict_types=1);

namespace PaymentProcessor\Components\TransactionsProviders\Definitions;

use PaymentProcessor\Entities\TransactionImmutableInterface;

interface DataProviderInterface extends \Iterator
{
    public function current(): TransactionImmutableInterface;
}
