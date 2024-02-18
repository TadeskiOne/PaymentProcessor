<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/../../../vendor/autoload.php';

set_error_handler(function ($code, $message, $file, $line) {
    throw new ErrorException($message, 0, $code, $file, $line);
});

(new Dotenv())->usePutenv()->loadEnv(__DIR__.'/../.env');
