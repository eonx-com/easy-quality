<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Exceptions\ThrowExceptionMessageSniff;

use EonX\EasyQuality\Sniffs\Exceptions\ThrowExceptionMessageSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class ThrowExceptionMessageSniffTest extends AbstractSniffTestCase
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
        yield 'Correct, no exception message' => [
            'filePath' => __DIR__ . '/Fixture/Correct/noExceptionMessage.php.inc',
        ];

        yield 'Correct, valid prefix' => [
            'filePath' => __DIR__ . '/Fixture/Correct/validPrefix.php.inc',
        ];

        yield 'Correct, multiline exception' => [
            'filePath' => __DIR__ . '/Fixture/Correct/multilineException.php.inc',
        ];

        yield 'Correct, throw variable' => [
            'filePath' => __DIR__ . '/Fixture/Correct/throwVariable.php.inc',
        ];

        yield 'Correct, variable message' => [
            'filePath' => __DIR__ . '/Fixture/Correct/variableMessage.php.inc',
        ];

        yield 'Wrong, hardcoded message' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/hardcodedMessage.php.inc',
            'expectedErrors' => [
                [
                    'line' => 4,
                    'code' => ThrowExceptionMessageSniff::class . '.ThrowExceptionMessageSniff',
                ],
            ],
        ];
    }
}
