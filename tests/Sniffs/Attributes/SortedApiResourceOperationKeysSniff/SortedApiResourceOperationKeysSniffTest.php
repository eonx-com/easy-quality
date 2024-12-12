<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Attributes\SortedApiResourceOperationKeysSniff;

use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

/**
 * @covers \EonX\EasyQuality\Sniffs\Attributes\SortedApiResourceOperationKeysSniff
 *
 * @internal
 */
final class SortedApiResourceOperationKeysSniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritdoc
     */
    public static function provideFixtures(): iterable
    {
        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/correct_associative_only.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/wrong_associative_only.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/correct_not_associative.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/wrong_not_associative.php.inc',
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
