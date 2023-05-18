<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Constants\DisallowApplicationConstantAndEnumUsageInTestAssertBlock;

use EonX\EasyQuality\Sniffs\Constants\DisallowApplicationConstantAndEnumUsageInTestAssertBlock;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class DisallowApplicationConstantAndEnumUsageInTestAssertBlockTest extends AbstractSniffTestCase
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
        yield 'correct, class usage' => [
            'filePath' => __DIR__ . '/Fixture/Correct/ClassUsage.php.inc',
        ];

        yield 'correct, static function call' => [
            'filePath' => __DIR__ . '/Fixture/Correct/StaticFunctionCall.php.inc',
        ];

        yield 'correct, not application constant usage' => [
            'filePath' => __DIR__ . '/Fixture/Correct/NotApplicationConstantUsage.php.inc',
        ];

        yield 'correct, self usage' => [
            'filePath' => __DIR__ . '/Fixture/Correct/SelfUsage.php.inc',
        ];

        yield 'wrong, application constant usage' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/DisallowedUsageConstant.php.inc',
            'expectedErrors' => [
                [
                    'line' => 10,
                    'code' => DisallowApplicationConstantAndEnumUsageInTestAssertBlock::class
                        . '.ApplicationConstantOrEnumUsedInAssertBlock',
                ],
            ],
        ];

        yield 'wrong, application enum usage' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/DisallowedUsageEnum.php.inc',
            'expectedErrors' => [
                [
                    'line' => 10,
                    'code' => DisallowApplicationConstantAndEnumUsageInTestAssertBlock::class
                        . '.ApplicationConstantOrEnumUsedInAssertBlock',
                ],
            ],
        ];
    }
}
