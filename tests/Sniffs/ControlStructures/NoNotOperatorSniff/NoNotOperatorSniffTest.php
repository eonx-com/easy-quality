<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\NoNotOperatorSniff;

use EonX\EasyQuality\Sniffs\ControlStructures\NoNotOperatorSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class NoNotOperatorSniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritDoc
     */
    public static function provideFixtures(): iterable
    {
        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/NoNotOperatorSniffTest.php.inc',
            'expectedErrors' => [
                [
                    'line' => 3,
                    'code' => NoNotOperatorSniff::class . '.NoNotOperator',
                ],
            ],
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
