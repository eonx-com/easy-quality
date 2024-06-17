<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\ArrangeActAssertSniff;

use EonX\EasyQuality\Sniffs\ControlStructures\ArrangeActAssertSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class ArrangeActAssertSniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritDoc
     */
    public static function provideFixtures(): iterable
    {
        yield 'Correct, abstract method' => [
            'filePath' => __DIR__ . '/Fixture/Correct/abstractMethod.php.inc',
        ];

        yield 'Correct, anonymous class with empty line' => [
            'filePath' => __DIR__ . '/Fixture/Correct/anonymousClassWithEmptyLine.php.inc',
        ];

        yield 'Correct, anonymous class without empty line' => [
            'filePath' => __DIR__ . '/Fixture/Correct/anonymousClassWithoutEmptyLine.php.inc',
        ];

        yield 'Correct, no test namespace' => [
            'filePath' => __DIR__ . '/Fixture/Correct/noTestNamespace.php.inc',
        ];

        yield 'Correct, empty lines' => [
            'filePath' => __DIR__ . '/Fixture/Correct/correctEmptyLines.php.inc',
        ];

        yield 'Correct, inline comment' => [
            'filePath' => __DIR__ . '/Fixture/Correct/inlineComment.php.inc',
        ];

        yield 'Correct, one line with comment' => [
            'filePath' => __DIR__ . '/Fixture/Correct/oneLineWithComment.php.inc',
        ];

        yield 'Correct, empty lines in closure' => [
            'filePath' => __DIR__ . '/Fixture/Correct/EmptyLinesInClosure.php.inc',
        ];

        yield 'Correct, inner curly brackets' => [
            'filePath' => __DIR__ . '/Fixture/Correct/innerCurlyBrackets.php.inc',
        ];

        yield 'Correct, multilevel closure' => [
            'filePath' => __DIR__ . '/Fixture/Correct/multiLevelClosure.php.inc',
        ];

        yield 'Correct, no test method' => [
            'filePath' => __DIR__ . '/Fixture/Correct/noTestMethod.php.inc',
        ];

        yield 'Correct, one line test method' => [
            'filePath' => __DIR__ . '/Fixture/Correct/oneLineTestMethod.php.inc',
        ];

        yield 'Correct, one multiLine' => [
            'filePath' => __DIR__ . '/Fixture/Correct/oneMultiLine.php.inc',
        ];

        yield 'Wrong, excessive empty lines' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/excessiveEmptyLines.php.inc',
            'expectedErrors' => [
                [
                    'line' => 8,
                    'code' => ArrangeActAssertSniff::class . '.ArrangeActAssertSniff',
                ],
            ],
        ];

        yield 'Wrong, no empty lines' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/noEmptyLines.php.inc',
            'expectedErrors' => [
                [
                    'line' => 8,
                    'code' => ArrangeActAssertSniff::class . '.ArrangeActAssertSniff',
                ],
            ],
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
