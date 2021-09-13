<?php

declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Classes\MakeClassAbstractSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services
        ->set(MakeClassAbstractSniff::class)
        ->property('applyTo', [
            [
                'namespace' => '/^EonX\\\EasyQuality\\\Tests\\\Sniffs\\\Classes\\\MakeClassAbstractSniff\\\Fixtures\\\(Correct|Wrong)$/',
                'patterns' => ['/.*TestCase$/'],
            ],
        ]);
};
