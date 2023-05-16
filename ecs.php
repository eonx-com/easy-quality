<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Arrays\AlphabeticallySortedArrayKeysSniff;
use EonX\EasyQuality\Sniffs\Attributes\SortedApiResourceOperationKeysSniff;
use EonX\EasyQuality\Sniffs\Classes\AvoidPublicPropertiesSniff;
use EonX\EasyQuality\Sniffs\Classes\MakeClassAbstractSniff;
use EonX\EasyQuality\Sniffs\ControlStructures\ArrangeActAssertSniff;
use EonX\EasyQuality\Sniffs\ControlStructures\LinebreakAfterEqualsSignSniff;
use EonX\EasyQuality\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff;
use EonX\EasyQuality\Sniffs\Methods\TestMethodNameSniff;
use EonX\EasyQuality\ValueObject\EasyQualitySetList;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff;
use PhpCsFixer\Fixer\ClassUsage\DateTimeImmutableFixer;
use PhpCsFixer\Fixer\LanguageConstruct\SingleSpaceAfterConstructFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use SlevomatCodingStandard\Sniffs\Classes\RequireSingleLineMethodSignatureSniff;
use SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff;
use SlevomatCodingStandard\Sniffs\Functions\StaticClosureSniff;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->sets([EasyQualitySetList::ECS]);
    $ecsConfig->parallel(120, 2, 1);
    $ecsConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/ecs.php',
    ]);
    $ecsConfig->skip([
        'tests/*/Fixture/*',
        AlphabeticallySortedArrayKeysSniff::class => [
            'tests/*',
        ],
        AvoidPublicPropertiesSniff::class => [
            'src/Sniffs/*',
        ],
        BlankLineAfterOpeningTagFixer::class => null,
        CamelCapsMethodNameSniff::class => [
            'src/Output/Printer.php',
        ],
        LineLengthSniff::class => [
            'ecs.php',
            'rector.php',
        ],
        SingleSpaceAfterConstructFixer::class => null,
    ]);

    $ecsConfig->rule(AlphabeticallySortedArrayKeysSniff::class);
    $ecsConfig->ruleWithConfiguration(ArrangeActAssertSniff::class, [
        'testNamespace' => 'Test',
    ]);
    $ecsConfig->rule(AvoidPublicPropertiesSniff::class);
    $ecsConfig->rule(DateTimeImmutableFixer::class);
    $ecsConfig->ruleWithConfiguration(DocCommentSpacingSniff::class, [
        'annotationsGroups' => [
            '@param',
            '@return ',
            '@throws',
            '@codeCoverageIgnore',
            '@covers',
            '@coversNothing',
            '@coversNothing',
            '@deprecated',
            '@method',
            '@noinspection',
            '@template',
            '@extends',
            '@property',
            '@see',
            '@SuppressWarnings',
            '@var',
        ],
        'linesCountBetweenAnnotationsGroups' => 1,
    ]);
    $ecsConfig->rule(LinebreakAfterEqualsSignSniff::class);
    $ecsConfig->ruleWithConfiguration(MakeClassAbstractSniff::class, [
        'applyTo' => [
            [
                'namespace' => '/^Test/',
                'patterns' => [
                    '/.*TestCase$/',
                ],
            ],
        ],
    ]);
    $ecsConfig->ruleWithConfiguration(RequireSingleLineMethodSignatureSniff::class, [
        'maxLineLength' => 120,
    ]);
    $ecsConfig->rule(SortedApiResourceOperationKeysSniff::class);
    $ecsConfig->rule(StandaloneLineInMultilineArrayFixer::class);
    $ecsConfig->rule(StaticClosureSniff::class);
    $ecsConfig->ruleWithConfiguration(TestMethodNameSniff::class, [
        'allowed' => [
            [
                'namespace' => '/^Test\\\(Unit|Integration)/',
                'patterns' => [
                    '/test[A-Z]/',
                    '/test.+(Succeeds|Throws.*Exception|DoesNothing|Returns.*)(If|When|With|On|And|For|$)/',
                ],
            ],
            [
                'namespace' => '/^Test\\\Application/',
                'patterns' => [
                    '/test[A-Z]/',
                    '/test.+(Succeeds|Fails|DoesNothing|Returns.*)(If|When|With|On|And|For|$)/',
                ],
            ],
        ],
        'forbidden' => [
            [
                'namespace' => '/^Test/',
                'patterns' => [
                    '/(Good|Bad|Wrong)[A-Z]/',
                    '/(?<!Succeeds|Fails|Exception|DoesNothing)(If|When|With|On)[A-Z].+(Succeeds|Fails|Throws|DoesNothing|Returns)/',
                ],
            ],
        ],
        'ignored' => [
            '/testWebhookSendFailsOnEachAttempt/',
            '/testOnFlushSucceeds/',
            '/testParsedWithErrorsSucceeds/',
            '/testSettersAndGetters/',
            '/testSignatureIsValid/',
            '/testVoteOnAttributeSucceeds/',
            '/testCountWithDifferentHashSucceeds/',
            '/testAccessDeniedWithJwtAuth/',
        ],
    ]);
    $ecsConfig->ruleWithConfiguration(UseYieldInsteadOfReturnSniff::class, [
        'applyTo' => [
            [
                'namespace' => '/^Test/',
                'patterns' => [
                    '/^provide[A-Z]*/',
                ],
            ],
        ],
    ]);
};
