<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Arrays\AlphabeticallySortedArrayKeysSniff;
use EonX\EasyQuality\Sniffs\Classes\AvoidPrivatePropertiesSniff;
use EonX\EasyQuality\Sniffs\Classes\AvoidPublicPropertiesSniff;
use EonX\EasyQuality\Sniffs\Classes\RequirePublicConstructorSniff;
use EonX\EasyQuality\Sniffs\Classes\RequireStrictDeclarationSniff;
use EonX\EasyQuality\Sniffs\Classes\StrictDeclarationFormatSniff;
use EonX\EasyQuality\Sniffs\Commenting\AnnotationSortingSniff;
use EonX\EasyQuality\Sniffs\ControlStructures\ArrangeActAssertSniff;
use EonX\EasyQuality\Sniffs\ControlStructures\LinebreakAfterEqualsSignSniff;
use EonX\EasyQuality\Sniffs\ControlStructures\NoNotOperatorSniff;
use EonX\EasyQuality\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff;
use EonX\EasyQuality\Sniffs\Exceptions\ThrowExceptionMessageSniff;
use EonX\EasyQuality\Sniffs\Functions\DisallowNonNullDefaultValueSniff;
use EonX\EasyQuality\Sniffs\Methods\TestMethodNameSniff;
use EonX\EasyQuality\Sniffs\Namespaces\Psr4Sniff;
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
                'namespace' => '/^Test\\\(Unit|Integration)/',
                'patterns' => [
                    '/test[A-Z]/',
                    '/test.+(Succeeds|Throws.*Exception|DoesNothing|Returns.*)(If|When|With|On|And|For|$)/',
                ],
            ],
            [
                'namespace' => '/^Test\\\Functional/',
                'patterns' => [
                    '/test[A-Z]/',
                    '/test.+(Succeeds|Fails|DoesNothing|Returns.*)(If|When|With|On|And|For|$)/',
                ],
            ],
        ])
        ->property('forbidden', [
            [
                'namespace' => '/^Test/',
                'patterns' => [
                    '/(Good|Bad|Wrong)[A-Z]/',
                    '/(?<!Succeeds|Fails|Exception|DoesNothing)(If|When|With|On)[A-Z].+(Succeeds|Fails|Throws|DoesNothing|Returns)/',
                ],
            ],
        ])
        ->property('ignored', [
            '/testWebhookSendFailsOnEachAttempt/',
            '/testOnFlushSucceeds/',
            '/testParsedWithErrorsSucceeds/',
            '/testSettersAndGetters/',
            '/testSignatureIsValid/',
            '/testVoteOnAttributeSucceeds/',
        ]);
    $services->set(UseYieldInsteadOfReturnSniff::class)
        ->property('applyTo', [
            [
                'namespace' => '/^Test/',
                'patterns' => [
                    '/provide[A-Z]*/',
                ],
            ],
        ]);
};
