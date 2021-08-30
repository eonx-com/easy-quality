<?php
declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DisallowShortOpenTagSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(DisallowLongArraySyntaxSniff::class);
    $services->set(LineLengthSniff::class)
        ->property('absoluteLineLimit', 120)
        ->property('ignoreComments', true);
    $services->set(DisallowShortOpenTagSniff::class);
    $services->set(CamelCapsMethodNameSniff::class);
};
