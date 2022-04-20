<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PHPStan\ThrowExceptionMessageRule;

use EonX\EasyQuality\PHPStan\ThrowExceptionMessageRule;
use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

/**
 * @covers \EonX\EasyQuality\PHPStan\ThrowExceptionMessageRule
 *
 * @internal
 */
final class ThrowExceptionMessageRuleTest extends AbstractServiceAwareRuleTestCase
{
    public function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ThrowExceptionMessageRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }

    public function provideData(): Iterator
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
     * @dataProvider provideData()
     *
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }
}