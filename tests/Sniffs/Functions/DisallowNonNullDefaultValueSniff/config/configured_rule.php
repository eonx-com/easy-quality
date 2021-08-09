<?php

declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Functions\DisallowNonNullDefaultValueSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(DisallowNonNullDefaultValueSniff::class);
};
