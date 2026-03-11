<?php
declare(strict_types=1);

use EonX\EasyQuality\Helper\ParallelSettingsHelper;
use EonX\EasyQuality\Rector\DataProviderSeeAnnotationRector;
use EonX\EasyQuality\Rector\SingleLineCommentRector;
use EonX\EasyQuality\ValueObject\EasyQualitySetList;
use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;

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
    ->withComposerBased(phpunit: true)
    ->withSets([
        EasyQualitySetList::RECTOR,
        EasyQualitySetList::RECTOR_PHPUNIT_12,
    ])
    ->withSkip([
        'tests/*/Fixture/*',
        AddOverrideAttributeToOverriddenMethodsRector::class,
        ClosureToArrowFunctionRector::class => [
            'tests/Sniffs/AbstractSniffTestCase.php',
        ],
        ReturnNeverTypeRector::class => [
            'tests/Sniffs/AbstractSniffTestCase.php',
        ],
    ])
    ->withRules([
        DataProviderSeeAnnotationRector::class,
    ])
    ->withConfiguredRule(SingleLineCommentRector::class, [[]]);
