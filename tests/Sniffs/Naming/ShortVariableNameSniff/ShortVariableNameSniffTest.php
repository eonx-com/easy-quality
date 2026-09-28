<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Naming\ShortVariableNameSniff;

use EonX\EasyQuality\Sniffs\Naming\ShortVariableNameSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class ShortVariableNameSniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritdoc
     */
    public static function provideFixtures(): iterable
    {
        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/ShortNames.php.inc',
            'expectedErrors' => [
                [
                    'line' => 8,
                    'code' => ShortVariableNameSniff::class . '.ShortVariableName',
                ],
                [
                    'line' => 10,
                    'code' => ShortVariableNameSniff::class . '.ShortVariableName',
                ],
                [
                    'line' => 16,
                    'code' => ShortVariableNameSniff::class . '.ShortVariableName',
                ],
                [
                    'line' => 24,
                    'code' => ShortVariableNameSniff::class . '.ShortVariableName',
                ],
                [
                    'line' => 29,
                    'code' => ShortVariableNameSniff::class . '.ShortVariableName',
                ],
                [
                    'line' => 34,
                    'code' => ShortVariableNameSniff::class . '.ShortVariableName',
                ],
            ],
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/ExceptedAndSkippedContexts.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/FixerLoopStability.php.inc',
            'expectedErrors' => [
                [
                    'line' => 10,
                    'code' => ShortVariableNameSniff::class . '.ShortVariableName',
                ],
            ],
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
