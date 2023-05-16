<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Attributes\SortAttributesAlphabeticallySniff;

use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class SortAttributesAlphabeticallySniffTest extends AbstractSniffTestCase
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
