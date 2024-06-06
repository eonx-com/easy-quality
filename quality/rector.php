<?php
declare(strict_types=1);

use EonX\EasyQuality\Rector\AddSeeAnnotationRector;
use EonX\EasyQuality\Rector\SingleLineCommentRector;
use EonX\EasyQuality\ValueObject\EasyQualitySetList;
use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/../config',
        __DIR__ . '/../src',
        __DIR__ . '/../tests',
        __DIR__ . '/ecs.php',
        __DIR__ . '/rector.php',
    ]);
    $rectorConfig->sets([
        EasyQualitySetList::RECTOR,
        EasyQualitySetList::RECTOR_PHPUNIT_10,
        LevelSetList::UP_TO_PHP_81,
    ]);
    $rectorConfig->importNames(true, false);
    $rectorConfig->importShortClasses();
    $rectorConfig->parallel(300, 2, 1);
    $rectorConfig->skip([
        'tests/*/Fixture/*',
        ClosureToArrowFunctionRector::class => [
            'tests/Sniffs/AbstractSniffTestCase.php',
        ],
        ReturnNeverTypeRector::class => [
            'tests/Sniffs/AbstractSniffTestCase.php',
        ],
    ]);

    $rectorConfig->rule(AddSeeAnnotationRector::class);
    $rectorConfig->ruleWithConfiguration(SingleLineCommentRector::class, [[]]);
};
