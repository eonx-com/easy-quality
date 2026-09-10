<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Files\LineLengthSniff;

use EonX\EasyQuality\Sniffs\Files\LineLengthSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class LineLengthSniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritdoc
     */
    public static function provideFixtures(): iterable
    {
        yield 'Wrong, long lines that can be wrapped' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/LongLines.php',
            'expectedErrors' => \array_map(
                static fn(int $line): array => [
                    'line' => $line,
                    'code' => LineLengthSniff::class . '.MaxExceeded',
                ],
                [8, 12, 13, 14, 15, 16, 17]
            ),
        ];

        yield 'Correct, long lines with references that do not fit even on their own line' => [
            'filePath' => __DIR__ . '/Fixture/Correct/UnbreakableReferences.php',
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
