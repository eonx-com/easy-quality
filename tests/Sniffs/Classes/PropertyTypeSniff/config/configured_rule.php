<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EonX\EasyQuality\Sniffs\Classes\PropertyTypeSniff;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services
        ->set(PropertyTypeSniff::class)
        ->property('replacePairs', [
            'DateTime' => 'DateTimeImmutable',
            'Carbon' => 'CarbonImmutable',
            'integer' => 'string',
        ]);
};
