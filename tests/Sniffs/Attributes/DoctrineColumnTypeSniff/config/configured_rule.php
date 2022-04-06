<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use EonX\EasyQuality\Sniffs\Attributes\DoctrineColumnTypeSniff;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services
        ->set(DoctrineColumnTypeSniff::class)
        ->property('replacePairs', [
            'date' => 'date_immutable',
            'datetime' => 'datetime_immutable',
            'Types::DATE' => 'Types::DATE_IMMUTABLE',
            'string' => 'Types::STRING',
        ]);
};
