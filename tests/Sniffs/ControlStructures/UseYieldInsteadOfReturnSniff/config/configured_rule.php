<?php

declare(strict_types=1);

use EonX\EasyQuality\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services
        ->set(UseYieldInsteadOfReturnSniff::class)
        ->property('applyTo', [
            [
                'namespace' => '/^EonX\\\EasyQuality\\\Tests\\\Sniffs\\\ControlStructures\\\UseYieldInsteadOfReturnSniff\\\Fixtures\\\(Correct|Wrong)$/',
                'patterns' => ['/provide[A-Z]*/'],
            ],
        ]);
};
