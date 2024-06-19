<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Functions\DisallowNonNullDefaultValueSniff;

use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class DisallowNonNullDefaultValueSniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritdoc
     */
    public static function provideFixtures(): iterable
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
        ];

        yield 'Wrong, closure multi line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/ClosureMultiLineParameters.php.inc',
        ];

        yield 'Wrong, simple function multi line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/SimpleFunctionMultiLineParameters.php.inc',
        ];

        yield 'Wrong, class method single line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/ClassMethodSingleLineParameters.php.inc',
        ];

        yield 'Wrong, closure single line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/ClosureSingleLineParameters.php.inc',
        ];

        yield 'Wrong, simple function single line parameters' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/SimpleFunctionSingleLineParameters.php.inc',
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
