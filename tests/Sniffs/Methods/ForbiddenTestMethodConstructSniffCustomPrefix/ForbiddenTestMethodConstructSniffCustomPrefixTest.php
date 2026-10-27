<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Methods\ForbiddenTestMethodConstructSniffCustomPrefix;

use EonX\EasyQuality\Sniffs\Methods\ForbiddenTestMethodConstructSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class ForbiddenTestMethodConstructSniffCustomPrefixTest extends AbstractSniffTestCase
{
    /**
     * @inheritdoc
     */
    public static function provideFixtures(): iterable
    {
        yield 'Correct, method with the default test prefix' => [
            'filePath' => __DIR__ . '/Fixture/Correct/defaultPrefixNotChecked.php.inc',
        ];

        yield 'Wrong, method with the configured prefix' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/customPrefix.php.inc',
            'expectedErrors' => [
                [
                    'line' => 12,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Ternary',
                ],
            ],
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
