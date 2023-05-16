<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Commenting\FunctionCommentSniff;

use EonX\EasyQuality\Sniffs\Commenting\FunctionCommentSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FunctionCommentSniffTest extends AbstractSniffTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }

    /**
     * @return iterable<\Symplify\SmartFileSystem\SmartFileInfo[]>
     */
    public function provideCorrectFixtures(): iterable
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/Correct/correct.php.inc')];
    }

    public function provideFixtures(): iterable
    {
        yield 'Correct' => [
            'filePath' => __DIR__ . '/Fixture/Correct/correct.php.inc',
        ];

        yield 'missingDocComment' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/missingDocComment.php.inc',
            'expectedErrors' => [
                [
                    'line' => 5,
                    'code' => FunctionCommentSniff::class . '.Missing',
                ],
            ],
        ];

        yield 'incorrectCommentStyle' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/incorrectCommentStyle.php.inc',
            'expectedErrors' => [
                [
                    'line' => 12,
                    'code' => FunctionCommentSniff::class . '.WrongStyle',
                ],
            ],
        ];

        yield 'blankLineAfterComment' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/blankLineAfterComment.php.inc',
            'expectedErrors' => [
                [
                    'line' => 11,
                    'code' => FunctionCommentSniff::class . '.SpacingAfter',
                ],
                [
                    'line' => 11,
                    'code' => FunctionCommentSniff::class . '.SpacingAfter',
                ],
            ],
        ];

        yield 'missingParamDocComment' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/missingParamDocComment.php.inc',
            'expectedErrors' => [
                [
                    'line' => 5,
                    'code' => FunctionCommentSniff::class . '.MissingParamTag',
                ],
            ],
        ];
    }
}
