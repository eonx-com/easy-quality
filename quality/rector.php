<?php
declare(strict_types=1);

use EonX\EasyQuality\Helper\ParallelSettingsHelper;
use EonX\EasyQuality\Rector\AddSeeAnnotationRector;
use EonX\EasyQuality\Rector\SingleLineCommentRector;
use EonX\EasyQuality\ValueObject\EasyQualitySetList;
use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php83\Rector\ClassConst\AddTypeToConstRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Php84\Rector\Foreach_\ForeachToArrayAnyRector;
use Rector\Php84\Rector\MethodCall\NewMethodCallWithoutParenthesesRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;

$temporaryIgnoredRectors = [
    AddTypeToConstRector::class,
    NewMethodCallWithoutParenthesesRector::class,
    ForeachToArrayAnyRector::class,
];

return RectorConfig::configure()
    ->withBootstrapFiles([
        __DIR__ . '/../stubs/PHP_CodeSniffer/Files/File.php',
    ])
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
    ->withImportNames(importDocBlockNames: false)
    ->withPhpSets(php84: true)
    ->withSets([
        EasyQualitySetList::RECTOR,
        EasyQualitySetList::RECTOR_PHPUNIT_10,
    ])
    ->withSkip([
        'tests/*/Fixture/*',
        AddOverrideAttributeToOverriddenMethodsRector::class,
        ...$temporaryIgnoredRectors,
        ClosureToArrowFunctionRector::class => [
            'tests/Sniffs/AbstractSniffTestCase.php',
        ],
        ReturnNeverTypeRector::class => [
            'tests/Sniffs/AbstractSniffTestCase.php',
        ],
    ])
    ->withRules([
        AddSeeAnnotationRector::class,
    ])
    ->withConfiguredRule(SingleLineCommentRector::class, [[]]);
