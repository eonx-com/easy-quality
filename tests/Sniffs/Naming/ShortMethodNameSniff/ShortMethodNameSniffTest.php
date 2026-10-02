<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Naming\ShortMethodNameSniff;

use EonX\EasyQuality\Sniffs\Naming\ShortMethodNameSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class ShortMethodNameSniffTest extends AbstractSniffTestCase
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
                    'code' => ShortMethodNameSniff::class . '.ShortMethodName',
                ],
                [
                    'line' => 13,
                    'code' => ShortMethodNameSniff::class . '.ShortMethodName',
                ],
            ],
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/NamesLongEnoughOrExceptedOrAnonymous.php.inc',
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
