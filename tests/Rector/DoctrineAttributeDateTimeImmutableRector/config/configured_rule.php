<?php

declare(strict_types=1);

use EonX\EasyQuality\Rector\DoctrineAttributeDateTimeImmutableRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services
        ->set(DoctrineAttributeDateTimeImmutableRector::class)
        ->call('configure', [
            [
                DoctrineAttributeDateTimeImmutableRector::REPLACE_TYPES => [
                    'date' => 'date_immutable',
                    'datetime' => 'datetime_immutable',
                ]
            ]
        ]);
};
