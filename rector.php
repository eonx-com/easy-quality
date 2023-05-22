<?php
declare(strict_types=1);

use EonX\EasyQuality\Rector\AddCoversAnnotationRector;
use EonX\EasyQuality\Rector\AddSeeAnnotationRector;
use EonX\EasyQuality\Rector\PhpDocCommentRector;
use EonX\EasyQuality\Rector\ReturnArrayToYieldRector;
use EonX\EasyQuality\Rector\SingleLineCommentRector;
use EonX\EasyQuality\Rector\UselessSingleAnnotationRector;
use EonX\EasyQuality\Rector\ValueObject\ReturnArrayToYield;
use EonX\EasyQuality\ValueObject\EasyQualitySetList;
use PHPUnit\Framework\TestCase;
use Rector\Config\RectorConfig;
use Rector\Php71\Rector\FuncCall\CountOnNullRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);
    $rectorConfig->sets([
        EasyQualitySetList::RECTOR,
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
        CountOnNullRector::class => null,
        ReturnNeverTypeRector::class => [
            'tests/Sniffs/AbstractSniffTestCase.php',
        ],
        UselessSingleAnnotationRector::class => [
            'src/Sniffs/Commenting/DocCommentSpacingSniff.php',
        ],
    ]);

    $rectorConfig->ruleWithConfiguration(AddCoversAnnotationRector::class, [
        AddCoversAnnotationRector::REPLACE_ARRAY => [],
    ]);
    $rectorConfig->rule(AddSeeAnnotationRector::class);
    $rectorConfig->ruleWithConfiguration(PhpDocCommentRector::class, [[]]);
    $rectorConfig->ruleWithConfiguration(ReturnArrayToYieldRector::class, [
        ReturnArrayToYieldRector::METHODS_TO_YIELDS => [
            new ReturnArrayToYield(TestCase::class, 'provide*'),
        ],
    ]);
    $rectorConfig->ruleWithConfiguration(SingleLineCommentRector::class, [[]]);
    $rectorConfig->ruleWithConfiguration(UselessSingleAnnotationRector::class, [
        UselessSingleAnnotationRector::ANNOTATIONS => ['{@inheritDoc}'],
    ]);
};
