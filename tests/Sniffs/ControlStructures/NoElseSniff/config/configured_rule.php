<?php

declare(strict_types=1);

use EonX\EasyQuality\Sniffs\ControlStructures\NoElseSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(NoElseSniff::class);
};
