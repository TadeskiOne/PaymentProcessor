<?php

declare(strict_types=1);

use PaymentProcessor\Apps\Custom\Commands\CalculateCommissionFeesCommand;

use function DI\autowire;

return [
    'app.name' => 'fees_calc',
    'app.version' => '1.0.0',
    'app.root' => realpath(__DIR__.'/../Custom/'),
    'app.commands' => [
        autowire(CalculateCommissionFeesCommand::class),
    ],
];
