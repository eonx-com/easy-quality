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
    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }

    /**
     * @inheritDoc
     */
    public function provideFixtures(): iterable
    {
        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/correct.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/wrong.php.inc',
        ];
    }
}
