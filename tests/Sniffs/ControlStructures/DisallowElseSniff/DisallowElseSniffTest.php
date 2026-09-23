<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\DisallowElseSniff;

use EonX\EasyQuality\Sniffs\ControlStructures\DisallowElseSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class DisallowElseSniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritdoc
     */
    public static function provideFixtures(): iterable
    {
        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/ElseAndElseif.php.inc',
            'expectedErrors' => [
                [
                    'line' => 8,
                    'code' => DisallowElseSniff::class . '.ElseFound',
                ],
                [
                    'line' => 10,
                    'code' => DisallowElseSniff::class . '.ElseFound',
                ],
            ],
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/EarlyReturn.php.inc',
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
