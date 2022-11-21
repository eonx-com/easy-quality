<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PHPStan\ThrowExceptionMessageRule;

use EonX\EasyQuality\PHPStan\ThrowExceptionMessageRule;
use Exception;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @covers \EonX\EasyQuality\PHPStan\ThrowExceptionMessageRule
 */
final class ThrowExceptionMessageRuleTest extends RuleTestCase
{
    public function getRule(): Rule
    {
        return new ThrowExceptionMessageRule(Exception::class, ['exceptions.']);
    }

    /**
     * @return iterable<mixed>
     *
     * @see testRule
     */
    public function provideData(): iterable
    {
        yield [__DIR__ . '/Fixture/correct/anotherExceptionType.php.inc', []];

        yield [__DIR__ . '/Fixture/correct/multilineException.php.inc', []];

        yield [__DIR__ . '/Fixture/correct/noExceptionMessage.php.inc', []];

        yield [__DIR__ . '/Fixture/correct/staticCall.php.inc', []];

        yield [__DIR__ . '/Fixture/correct/throwVariable.php.inc', []];

        yield [__DIR__ . '/Fixture/correct/validPrefix.php.inc', []];

        yield [__DIR__ . '/Fixture/correct/variableMessage.php.inc', []];

        $errorMessage = \sprintf(ThrowExceptionMessageRule::ERROR_MESSAGE, 'exceptions.');
        yield [__DIR__ . '/Fixture/wrong/hardcodedMessage.php.inc', [[$errorMessage, 4]]];
    }

    /**
     * @param array<string|int> $expectedErrorMessagesWithLines
     *
     * @dataProvider provideData
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }
}
