<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\AvoidPublicPropertiesSniff;

use EonX\EasyQuality\Sniffs\Classes\AvoidPublicPropertiesSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class AvoidPublicPropertiesSniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritDoc
     */
    public static function provideFixtures(): iterable
    {
        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/AvoidPublicPropertiesSniffTest.php.inc',
            'expectedErrors' => [
                [
                    'line' => 5,
                    'code' => AvoidPublicPropertiesSniff::class . '.InvalidScope',
                ],
            ],
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
