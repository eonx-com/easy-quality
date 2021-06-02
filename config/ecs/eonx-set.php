<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniff\Arrays\AlphabeticallySortedArrayKeysSniff;
use EonX\EasyQuality\Sniff\Classes\AvoidPrivatePropertiesSniff;
use EonX\EasyQuality\Sniff\Classes\AvoidPublicPropertiesSniff;
use EonX\EasyQuality\Sniff\Classes\RequirePublicConstructorSniff;
use EonX\EasyQuality\Sniff\Classes\RequireStrictDeclarationSniff;
use EonX\EasyQuality\Sniff\Classes\StrictDeclarationFormatSniff;
use EonX\EasyQuality\Sniff\Commenting\AnnotationSortingSniff;
use EonX\EasyQuality\Sniff\ControlStructures\ArrangeActAssertSniff;
use EonX\EasyQuality\Sniff\ControlStructures\LinebreakAfterEqualsSignSniff;
use EonX\EasyQuality\Sniff\ControlStructures\NoNotOperatorSniff;
use EonX\EasyQuality\Sniff\Exceptions\ThrowExceptionMessageSniff;
use EonX\EasyQuality\Sniff\Functions\DisallowNonNullDefaultValueSniff;
use EonX\EasyQuality\Sniff\Methods\TestMethodNameSniff;
use EonX\EasyQuality\Sniff\Namespaces\Psr4Sniff;
use PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(LinebreakAfterEqualsSignSniff::class);
    $services->set(AvoidPrivatePropertiesSniff::class);
    $services->set(AlphabeticallySortedArrayKeysSniff::class);
    $services->set(RequireStrictDeclarationSniff::class);
    $services->set(StrictDeclarationFormatSniff::class);
    $services->set(RequirePublicConstructorSniff::class);
    $services->set(AvoidPublicPropertiesSniff::class);
    $services->set(AnnotationSortingSniff::class)
        ->property('alwaysTopAnnotations', ['@param', '@return', '@throws']);
    $services->set(ArrangeActAssertSniff::class);
    $services->set(NoNotOperatorSniff::class);
    $services->set(ThrowExceptionMessageSniff::class);
    $services->set(Psr4Sniff::class);
    $services->set(DisallowNonNullDefaultValueSniff::class);
    $services->set(TestMethodNameSniff::class)
        ->property('allowed', [
            [
                'namespace' => '/^App\\\Tests\\\(Unit|Integration)/',
                'patterns' => [
                    '/test[A-Z]/',
                    '/test.+(Succeeds|Throws.*Exception|DoesNothing|Returns.*)(If|When|With|On|And|For|$)/',
                ],
            ],
            [
                'namespace' => '/^App\\\Tests\\\Functional/',
                'patterns' => [
                    '/test[A-Z]/',
                    '/test.+(Succeeds|Fails|DoesNothing|Returns.*)(If|When|With|On|And|For|$)/',
                ],
            ],
        ])
        ->property('forbidden', [
            [
                'namespace' => '/^App\\\Tests/',
                'patterns' => [
                    '/(Good|Bad|Wrong)[A-Z]/',
                    '/(?<!Succeeds|Fails|Exception|DoesNothing)(If|When|With|On)[A-Z].+(Succeeds|Fails|Throws|DoesNothing|Returns)/',
                ],
            ],
        ]);
};
