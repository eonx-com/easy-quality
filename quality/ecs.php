<?php
declare(strict_types=1);

use EonX\EasyQuality\Helper\ParallelSettingsHelper;
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
use SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/../config',
        __DIR__ . '/../src',
        __DIR__ . '/../tests',
        __DIR__ . '/ecs.php',
        __DIR__ . '/rector.php',
    ])
    ->withParallel(
        ParallelSettingsHelper::getTimeoutSeconds(),
        ParallelSettingsHelper::getMaxNumberOfProcess(),
        ParallelSettingsHelper::getJobSize()
    )
    ->withSets([EasyQualitySetList::ECS])
    ->withSkip([
        'tests/*/Fixture/*',
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
        PropertyTypeHintSniff::class . '.MissingTraversableTypeHintSpecification' => null,
        SingleSpaceAfterConstructFixer::class => null,
    ])
    ->withRules([
        AvoidPublicPropertiesSniff::class,
        DateTimeImmutableFixer::class,
        LinebreakAfterEqualsSignSniff::class,
        SortedApiResourceOperationKeysSniff::class,
        StandaloneLineInMultilineArrayFixer::class,
        StaticClosureSniff::class,
    ])
    ->withConfiguredRule(AlphabeticallySortedArrayKeysSniff::class, [
        'skipPatterns' => [
            T_FUNCTION => ['/^provide/'],
        ],
    ])
    ->withConfiguredRule(ArrangeActAssertSniff::class, [
        'testNamespace' => 'Test',
    ])
    ->withConfiguredRule(DocCommentSpacingSniff::class, [
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
    ])
    ->withConfiguredRule(MakeClassAbstractSniff::class, [
        'applyTo' => [
            [
                'namespace' => '/^Test/',
                'patterns' => [
                    '/.*TestCase$/',
                ],
            ],
        ],
    ])
    ->withConfiguredRule(RequireSingleLineMethodSignatureSniff::class, [
        'maxLineLength' => 120,
    ])
    ->withConfiguredRule(TestMethodNameSniff::class, [
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
    ])
    ->withConfiguredRule(UseYieldInsteadOfReturnSniff::class, [
        'applyTo' => [
            [
                'namespace' => '/^Test/',
                'patterns' => [
                    '/^provide[A-Z]*/',
                ],
            ],
        ],
    ]);
