<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PhpStan\ClassInheritanceRule;

use EonX\EasyQuality\PhpStan\ClassInheritanceRule;
use EonX\EasyQuality\Tests\PhpStan\ClassInheritanceRule\Stub\SomeAbstractClass;
use EonX\EasyQuality\Tests\PhpStan\ClassInheritanceRule\Stub\SomeInterface;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @extends \PHPStan\Testing\RuleTestCase<\EonX\EasyQuality\PhpStan\ClassInheritanceRule>
 */
final class ClassInheritanceRuleTest extends RuleTestCase
{
    /**
     * @see testRule
     */
    public static function provideData(): iterable
    {
        yield 'correct class that not match any rule' => [
            __DIR__ . '/Fixture/correct/FixtureEmptyClass.php',
            [],
        ];

        yield 'correct class that implements interface' => [
            __DIR__ . '/Fixture/correct/FixtureImplementsInterfaceCase1.php',
            [],
        ];

        yield 'correct class that extends abstract class' => [
            __DIR__ . '/Fixture/correct/FixtureExtendClassCase2.php',
            [],
        ];

        yield 'correct class that implements interface and extends abstract class' => [
            __DIR__ . '/Fixture/correct/FixtureImplementsInterfaceAndExtendClassCase3.php',
            [],
        ];

        yield 'correct class that implements interface and not extends abstract class' => [
            __DIR__ . '/Fixture/correct/FixtureImplementsInterfaceAndNotExtendClassCase3.php',
            [],
        ];

        yield 'correct class that not implements interface and extends abstract class' => [
            __DIR__ . '/Fixture/correct/FixtureNotImplementsInterfaceAndExtendClassCase3.php',
            [],
        ];

        yield 'wrong class that not implements interface' => [
            __DIR__ . '/Fixture/wrong/FixtureNotImplementsInterfaceCase1.php',
            [
                [
                    'Class EonX\EasyQuality\Tests\PhpStan\ClassInheritanceRule\Fixture\wrong' .
                    '\FixtureNotImplementsInterfaceCase1 must implement or extend one of' .
                    ': EonX\EasyQuality\Tests\PhpStan\ClassInheritanceRule\Stub\SomeInterface.',
                    6,
                ],
            ],
        ];

        yield 'wrong class that not extends abstract class' => [
            __DIR__ . '/Fixture/wrong/FixtureNotExtendClassCase2.php',
            [
                [
                    'Class EonX\EasyQuality\Tests\PhpStan\ClassInheritanceRule\Fixture\wrong' .
                    '\FixtureNotExtendClassCase2 must implement or extend one of' .
                    ': EonX\EasyQuality\Tests\PhpStan\ClassInheritanceRule\Stub\SomeAbstractClass.',
                    6,
                ],
            ],
        ];

        yield 'wrong class that not implements interface and not extends abstract class' => [
            __DIR__ . '/Fixture/wrong/FixtureNotImplementsInterfaceAndNotExtendClassCase3.php',
            [
                [
                    'Class EonX\EasyQuality\Tests\PhpStan\ClassInheritanceRule\Fixture\wrong' .
                    '\FixtureNotImplementsInterfaceAndNotExtendClassCase3 must implement or extend one of' .
                    ': EonX\EasyQuality\Tests\PhpStan\ClassInheritanceRule\Stub\SomeInterface, ' .
                    'EonX\EasyQuality\Tests\PhpStan\ClassInheritanceRule\Stub\SomeAbstractClass.',
                    6,
                ],
            ],
        ];
    }

    public function getRule(): Rule
    {
        /** @var \PHPStan\Reflection\ReflectionProvider $reflectionProvider */
        $reflectionProvider = self::getContainer()->getService('reflectionProvider');

        return new ClassInheritanceRule($reflectionProvider, [
            '/\\\Fixture.*Case1$/' => [
                SomeInterface::class,
            ],
            '/\\\Fixture.*Case2$/' => [
                SomeAbstractClass::class,
            ],
            '/\\\Fixture.*Case3$/' => [
                SomeInterface::class,
                SomeAbstractClass::class,
            ],
        ]);
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
