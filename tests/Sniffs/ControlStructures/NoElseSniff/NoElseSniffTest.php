<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\NoElseSniff;

use EonX\EasyQuality\Sniffs\ControlStructures\NoElseSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class NoElseSniffTest extends AbstractSniffTestCase
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
            'filePath' => __DIR__ . '/Fixture/Wrong/NoElseSniffTest.php.inc',
            'expectedErrors' => [
                [
                    'line' => 5,
                    'code' => NoElseSniff::class . '.NoElse',
                ],
            ],
        ];
    }
}
