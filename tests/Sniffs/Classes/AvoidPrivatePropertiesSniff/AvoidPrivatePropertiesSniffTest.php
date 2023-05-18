<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\AvoidPrivatePropertiesSniff;

use EonX\EasyQuality\Sniffs\Classes\AvoidPrivatePropertiesSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class AvoidPrivatePropertiesSniffTest extends AbstractSniffTestCase
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
            'filePath' => __DIR__ . '/Fixture/Correct/ProtectedProperties.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/PublicProperties.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/PrivateProperties.php.inc',
            'expectedErrors' => [
                [
                    'line' => 5,
                    'code' => AvoidPrivatePropertiesSniff::class . '.InvalidScope',
                ],
            ],
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/MissedScopeProperties.php.inc',
            'expectedErrors' => [
                [
                    'line' => 5,
                    'code' => AvoidPrivatePropertiesSniff::class . '.ScopeMissing',
                ],
            ],
        ];
    }
}
