<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Functions\DisallowNonNullDefaultValueSniff;

use EonX\EasyQuality\Sniffs\Functions\DisallowNonNullDefaultValueSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class DisallowNonNullDefaultValueSniffTest extends AbstractSniffTestCase
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
        yield 'Correct, class method with promoted properties in constructor' => [
            'filePath' => __DIR__ . '/Fixture/Correct/ClassMethodWithPromotedPropertiesInConstructor.php.inc',
        ];

        yield 'Correct, class method multi line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Correct/ClassMethodMultiLineParameters.php.inc',
        ];

        yield 'Correct, closure multi line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Correct/ClosureMultiLineParameters.php.inc',
        ];

        yield 'Correct, simple function multi line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Correct/SimpleFunctionMultiLineParameters.php.inc',
        ];

        yield 'Correct, class method multi line with read only parameters in constructor' => [
            'filePath' => __DIR__ . '/Fixture/Correct/ClassMethodMultiLineWithReadOnlyParametersInConstructor.php.inc',
        ];

        yield 'Correct, class method single line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Correct/ClassMethodSingleLineParameters.php.inc',
        ];

        yield 'Correct, closure single line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Correct/ClosureSingleLineParameters.php.inc',
        ];

        yield 'Correct, simple function single line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Correct/SimpleFunctionSingleLineParameters.php.inc',
        ];

        yield 'Wrong, class method multi line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/ClassMethodMultiLineParameters.php.inc',
            'expectedErrors' => [
                [
                    'line' => 18,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 19,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 20,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 21,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 23,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 24,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 25,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.MissedDefaultValue',
                ],
                [
                    'line' => 26,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 27,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 28,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
            ],
        ];

        yield 'Wrong, closure multi line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/ClosureMultiLineParameters.php.inc',
            'expectedErrors' => [
                [
                    'line' => 14,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 15,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 16,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 17,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 19,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 20,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 21,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.MissedDefaultValue',
                ],
                [
                    'line' => 22,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 23,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 24,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
            ],
        ];

        yield 'Wrong, simple function multi line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/SimpleFunctionMultiLineParameters.php.inc',
            'expectedErrors' => [
                [
                    'line' => 12,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 13,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 14,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 15,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 17,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 18,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 19,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 20,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.MissedDefaultValue',
                ],
                [
                    'line' => 21,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 22,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 23,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
            ],
        ];

        yield 'Wrong, class method single line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/ClassMethodSingleLineParameters.php.inc',
            'expectedErrors' => [
                [
                    'line' => 15,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.MissedDefaultValue',
                ],
                [
                    'line' => 15,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 15,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
            ],
        ];

        yield 'Wrong, closure single line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/ClosureSingleLineParameters.php.inc',
            'expectedErrors' => [
                [
                    'line' => 11,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.MissedDefaultValue',
                ],
                [
                    'line' => 11,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 11,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
            ],
        ];

        yield 'Wrong, simple function single line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/SimpleFunctionSingleLineParameters.php.inc',
            'expectedErrors' => [
                [
                    'line' => 9,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.MissedDefaultValue',
                ],
                [
                    'line' => 9,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
                [
                    'line' => 9,
                    'code' => DisallowNonNullDefaultValueSniff::class . '.IncorrectDefaultValue',
                ],
            ],
        ];
    }
}
