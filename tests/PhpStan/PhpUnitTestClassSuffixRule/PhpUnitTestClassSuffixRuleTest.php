<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PhpStan\PhpUnitTestClassSuffixRule;

use EonX\EasyQuality\PhpStan\PhpUnitTestClassSuffixRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @extends \PHPStan\Testing\RuleTestCase<\EonX\EasyQuality\PhpStan\PhpUnitTestClassSuffixRule>
 */
final class PhpUnitTestClassSuffixRuleTest extends RuleTestCase
{
    /**
     * @see testRule
     */
    public static function provideData(): iterable
    {
        yield 'correct class not test' => [
            __DIR__ . '/Fixture/correct/SomeClass.php',
            [],
        ];

        yield 'correct abstract test case that extends test case' => [
            __DIR__ . '/Fixture/correct/AbstractSomeTestCase.php',
            [],
        ];

        yield 'correct test that extends test case' => [
            __DIR__ . '/Fixture/correct/SomeClassTest.php',
            [],
        ];

        yield 'wrong abstract test case that extends test case ' => [
            __DIR__ . '/Fixture/wrong/AbstractTest.php',
            [
                [
                    'PHPUnit test case class must have a `TestCase` suffix.',
                    8,
                ],
            ],
        ];

        yield 'wrong test that extends test case' => [
            __DIR__ . '/Fixture/wrong/SomeClass.php',
            [
                [
                    'PHPUnit test class must have a `Test` suffix.',
                    8,
                ],
            ],
        ];
    }

    public function getRule(): Rule
    {
        /** @var \PHPStan\Reflection\ReflectionProvider $reflectionProvider */
        $reflectionProvider = self::getContainer()->getService('reflectionProvider');

        return new PhpUnitTestClassSuffixRule($reflectionProvider);
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
