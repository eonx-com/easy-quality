<?php

declare(strict_types=1);

use EonX\EasyQuality\Rector\AddCoversAnnotationRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services
        ->set(AddCoversAnnotationRector::class)
        ->call('configure', [
            [
                AddCoversAnnotationRector::REPLACE_ARRAY => ['Tests\\Unit\\'],
            ]
        ]);
};
