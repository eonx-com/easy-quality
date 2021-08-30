<?php

declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Classes\RequireStrictDeclarationSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(RequireStrictDeclarationSniff::class);
};
