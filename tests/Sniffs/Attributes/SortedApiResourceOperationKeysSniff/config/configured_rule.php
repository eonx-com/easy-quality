<?php

declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Attributes\SortedApiResourceOperationKeysSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services
        ->set(SortedApiResourceOperationKeysSniff::class);
};
