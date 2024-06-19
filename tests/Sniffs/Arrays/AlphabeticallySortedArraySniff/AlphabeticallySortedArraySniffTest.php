<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Arrays\AlphabeticallySortedArraySniff;

use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class AlphabeticallySortedArraySniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritdoc
     */
    public static function provideFixtures(): iterable
    {
        yield 'Correct, multi line array' => [
            'filePath' => __DIR__ . '/Fixture/Correct/MultiLineArray.php.inc',
        ];

        yield 'Correct, multi line mixed array' => [
            'filePath' => __DIR__ . '/Fixture/Correct/MultiLineMixedArray.php.inc',
        ];

        yield 'Correct, multi line multi dimensional array' => [
            'filePath' => __DIR__ . '/Fixture/Correct/MultiLineMultiDimensionalArray.php.inc',
        ];

        yield 'Correct, single line array' => [
            'filePath' => __DIR__ . '/Fixture/Correct/SingleLineArray.php.inc',
        ];

        yield 'Correct, single line mixed array' => [
            'filePath' => __DIR__ . '/Fixture/Correct/SingleLineMixedArray.php.inc',
        ];

        yield 'Correct, single line multi dimensional array' => [
            'filePath' => __DIR__ . '/Fixture/Correct/SingleLineMultiDimensionalArray.php.inc',
        ];

        yield 'Correct, skip by class name' => [
            'filePath' => __DIR__ . '/Fixture/Correct/SkipByClassName.php.inc',
        ];

        yield 'Correct, skip by function name' => [
            'filePath' => __DIR__ . '/Fixture/Correct/SkipByFunctionName.php.inc',
        ];

        yield 'Wrong, multi line array' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/MultiLineArray.php.inc',
        ];

        yield 'Wrong, multi line mixed array' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/MultiLineMixedArray.php.inc',
        ];

        yield 'Wrong, multi line multi dimensional array' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/MultiLineMultiDimensionalArray.php.inc',
        ];

        yield 'Wrong, single line array' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/SingleLineArray.php.inc',
        ];

        yield 'Wrong, single line mixed array' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/SingleLineMixedArray.php.inc',
        ];

        yield 'Wrong, single line multi dimensional array' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/SingleLineMultiDimensionalArray.php.inc',
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
