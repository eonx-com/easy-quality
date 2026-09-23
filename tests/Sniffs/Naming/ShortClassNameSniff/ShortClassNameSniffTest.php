<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Naming\ShortClassNameSniff;

use EonX\EasyQuality\Sniffs\Naming\ShortClassNameSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class ShortClassNameSniffTest extends AbstractSniffTestCase
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
                    'line' => 6,
                    'code' => ShortClassNameSniff::class . '.ShortClassName',
                ],
                [
                    'line' => 10,
                    'code' => ShortClassNameSniff::class . '.ShortClassName',
                ],
            ],
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/NamesLongEnoughOrExcepted.php.inc',
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
