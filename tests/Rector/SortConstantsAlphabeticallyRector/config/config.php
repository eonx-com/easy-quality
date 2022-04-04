<?php

declare(strict_types=1);

use EonX\EasyQuality\Rector\Order\StmtOrder;
use EonX\EasyQuality\Rector\SortConstantsAlphabeticallyRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire();

    $services->set(StmtOrder::class);

    $services->set(SortConstantsAlphabeticallyRector::class);
};
