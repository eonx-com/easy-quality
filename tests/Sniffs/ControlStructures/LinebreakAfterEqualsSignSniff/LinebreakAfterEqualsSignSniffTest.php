<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\LinebreakAfterEqualsSignSniff;

use EonX\EasyQuality\Sniffs\ControlStructures\LinebreakAfterEqualsSignSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class LinebreakAfterEqualsSignSniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritDoc
     */
    public static function provideFixtures(): iterable
    {
        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/LinebreakAfterEqualsSignSniffTest.php.inc',
            'expectedErrors' => [
                [
                    'line' => 3,
                    'code' => LinebreakAfterEqualsSignSniff::class . '.LinebreakAfterEqualsSign',
                ],
            ],
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
