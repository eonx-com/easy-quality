<?php
declare(strict_types=1);

use EonX\EasyQuality\Helper\ParallelSettingsResolver;
use EonX\EasyQuality\Rector\AddSeeAnnotationRector;
use EonX\EasyQuality\Rector\SingleLineCommentRector;
use EonX\EasyQuality\ValueObject\EasyQualitySetList;
use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/../config',
        __DIR__ . '/../src',
        __DIR__ . '/../tests',
        __DIR__ . '/ecs.php',
        __DIR__ . '/rector.php',
    ])
    ->withParallel(
        ParallelSettingsResolver::resolveTimeoutSeconds(),
        ParallelSettingsResolver::resolveMaxNumberOfProcess(),
        ParallelSettingsResolver::resolveJobSize()
    )
    ->withImportNames(importDocBlockNames: false)
    ->withPhpSets(php81: true)
    ->withSets([
        EasyQualitySetList::RECTOR,
        EasyQualitySetList::RECTOR_PHPUNIT_10,
    ])
    ->withSkip([
        'tests/*/Fixture/*',
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
