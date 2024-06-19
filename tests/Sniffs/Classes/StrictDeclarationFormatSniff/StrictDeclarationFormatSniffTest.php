<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\StrictDeclarationFormatSniff;

use EonX\EasyQuality\Sniffs\Classes\StrictDeclarationFormatSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class StrictDeclarationFormatSniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritdoc
     */
    public static function provideFixtures(): iterable
    {
        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/StrictDeclarationFormatSniffTest_ExtraLine.php.inc',
            'expectedErrors' => [
                [
                    'line' => 1,
                    'code' => StrictDeclarationFormatSniff::class . '.StrictDeclarationFormat',
                ],
            ],
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/StrictDeclarationFormatSniffTest_SameLine.php.inc',
            'expectedErrors' => [
                [
                    'line' => 1,
                    'code' => StrictDeclarationFormatSniff::class . '.StrictDeclarationFormat',
                ],
                [
                    'line' => 1,
                    'code' => StrictDeclarationFormatSniff::class . '.StrictDeclarationFormat',
                ],
            ],
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/StrictDeclarationFormatSniffTest_WrongFormat.php.inc',
            'expectedErrors' => [
                [
                    'line' => 1,
                    'code' => StrictDeclarationFormatSniff::class . '.StrictDeclarationFormat',
                ],
            ],
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
