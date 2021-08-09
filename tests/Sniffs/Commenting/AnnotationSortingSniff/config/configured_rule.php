<?php

declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Commenting\AnnotationSortingSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services
        ->set(AnnotationSortingSniff::class)
        ->property('alwaysTopAnnotations', ['@param', '@return', '@throws']);
};
