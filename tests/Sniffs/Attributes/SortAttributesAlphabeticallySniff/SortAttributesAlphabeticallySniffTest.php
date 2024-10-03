<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Attributes\SortAttributesAlphabeticallySniff;

use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class SortAttributesAlphabeticallySniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritdoc
     */
    public static function provideFixtures(): iterable
    {
        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/correct.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/different_case.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/wrong.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/different_case.php.inc',
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
