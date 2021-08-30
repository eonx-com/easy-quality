<?php

declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Exceptions\ThrowExceptionMessageSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services
        ->set(ThrowExceptionMessageSniff::class)
        ->property('validPrefixes', ['exceptions']);
};
