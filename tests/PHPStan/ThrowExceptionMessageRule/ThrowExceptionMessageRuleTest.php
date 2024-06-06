<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PHPStan\ThrowExceptionMessageRule;

use EonX\EasyQuality\PHPStan\ThrowExceptionMessageRule;
use Exception;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @extends \PHPStan\Testing\RuleTestCase<\EonX\EasyQuality\PHPStan\ThrowExceptionMessageRule>
 */
#[CoversClass(ThrowExceptionMessageRule::class)]
final class ThrowExceptionMessageRuleTest extends RuleTestCase
{
    /**
     * @see testRule
     */
    public static function provideData(): iterable
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

    public function getRule(): Rule
    {
        return new ThrowExceptionMessageRule(Exception::class, ['exceptions.']);
    }

    /**
     * @param array<int, array{0: string, 1: int, 2?: string}> $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }
}
