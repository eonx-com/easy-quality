<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\RequirePublicConstructorSniff;

use EonX\EasyQuality\Sniffs\Classes\RequirePublicConstructorSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class RequirePublicConstructorSniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritdoc
     */
    public static function provideFixtures(): iterable
    {
        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/RequirePublicConstructorSniffTest.php.inc',
            'expectedErrors' => [
                [
                    'line' => 5,
                    'code' => RequirePublicConstructorSniff::class . '.RequirePublicConstructor',
                ],
            ],
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
