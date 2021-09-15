<?php

declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Commenting\DocCommentSpacingSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(DocCommentSpacingSniff::class)
        ->property('linesCountBetweenAnnotationsGroups', 1)
        ->property('annotationsGroups', [
            '@AppAssert*',
            '@Assert\\',
            '@param',
            '@return'
        ]);
};
