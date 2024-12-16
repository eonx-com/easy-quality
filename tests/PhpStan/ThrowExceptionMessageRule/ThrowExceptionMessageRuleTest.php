<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PhpStan\ThrowExceptionMessageRule;

use EonX\EasyQuality\PhpStan\ThrowExceptionMessageRule;
use LogicException;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @extends \PHPStan\Testing\RuleTestCase<\EonX\EasyQuality\PhpStan\ThrowExceptionMessageRule>
 */
#[CoversClass(ThrowExceptionMessageRule::class)]
final class ThrowExceptionMessageRuleTest extends RuleTestCase
{
    /**
     * @see testRule
     */
    public static function provideData(): iterable
    {
        yield 'correct anotherExceptionType.php.inc' => [
            __DIR__ . '/Fixture/correct/anotherExceptionType.php.inc',
            [],
        ];

        yield 'correct multilineException.php.inc' => [
            __DIR__ . '/Fixture/correct/multilineException.php.inc',
            [],
        ];

        yield 'correct noExceptionMessage.php.inc' => [
            __DIR__ . '/Fixture/correct/noExceptionMessage.php.inc',
            [],
        ];

        yield 'correct staticCall.php.inc' => [
            __DIR__ . '/Fixture/correct/staticCall.php.inc',
            [],
        ];

        yield 'correct throwVariable.php.inc' => [
            __DIR__ . '/Fixture/correct/throwVariable.php.inc',
            [],
        ];

        yield 'correct validPrefix.php.inc' => [
            __DIR__ . '/Fixture/correct/validPrefix.php.inc',
            [],
        ];

        yield 'correct variableMessage.php.inc' => [
            __DIR__ . '/Fixture/correct/variableMessage.php.inc',
            [],
        ];

        yield 'wrong hardcodedMessage.php.inc' => [
            __DIR__ . '/Fixture/wrong/hardcodedMessage.php.inc',
            [
                [
                    \sprintf(ThrowExceptionMessageRule::ERROR_MESSAGE, 'exceptions.'),
                    4,
                ],
            ],
        ];
    }

    public function getRule(): Rule
    {
        /** @var \PHPStan\Reflection\ReflectionProvider $reflectionProvider */
        $reflectionProvider = self::getContainer()->getService('reflectionProvider');

        return new ThrowExceptionMessageRule(LogicException::class, $reflectionProvider);
    }

    /**
     * @param list<array{0: string, 1: int, 2?: string}> $expectedErrorMessagesWithLines
     */
    #[DataProvider('provideData')]
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }
}
