<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\LineLengthSniff;

use EonX\EasyQuality\Sniffs\ControlStructures\LineLengthSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class LineLengthSniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritdoc
     */
    public static function provideFixtures(): iterable
    {
        yield 'Wrong, long lines without constants, enum cases or static method calls' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/LongLines.php',
            'expectedErrors' => [
                [
                    'line' => 10,
                    'code' => LineLengthSniff::class . '.MaxExceeded',
                ],
                [
                    'line' => 11,
                    'code' => LineLengthSniff::class . '.MaxExceeded',
                ],
                [
                    'line' => 12,
                    'code' => LineLengthSniff::class . '.MaxExceeded',
                ],
            ],
        ];

        yield 'Correct, long lines with constants, enum cases and static method calls are ignored' => [
            'filePath' => __DIR__ . '/Fixture/Correct/IgnoredConstantsAndEnums.php',
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
