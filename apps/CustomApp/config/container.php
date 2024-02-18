<?php

declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;

return (function (): Container {
    $builder = new ContainerBuilder();
    $builder->useAutowiring(true);
    $builder->useAnnotations(false);

    $builder->addDefinitions(
        ...glob(__DIR__.'/definitions/*.php'),
    );

    return $builder->build();
})();
