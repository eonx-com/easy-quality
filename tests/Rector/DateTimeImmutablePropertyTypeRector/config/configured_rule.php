<?php

declare(strict_types=1);

use EonX\EasyQuality\Rector\DateTimeImmutablePropertyTypeRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(DateTimeImmutablePropertyTypeRector::class);
    $services
        ->set(DateTimeImmutablePropertyTypeRector::class)
        ->call('configure', [
            [
                DateTimeImmutablePropertyTypeRector::REPLACE_PAIRS => [
                    'DateTime' => 'DateTimeImmutable',
                    'Carbon\\Carbon' => 'Carbon\\CarbonImmutable',
                ]
            ]
        ]);
};
