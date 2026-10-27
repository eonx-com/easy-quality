<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Methods\ForbiddenTestMethodConstructSniff;

use EonX\EasyQuality\Sniffs\Methods\ForbiddenTestMethodConstructSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Configuration;

final class ForbiddenTestMethodConstructSniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritdoc
     */
    public static function provideFixtures(): iterable
    {
        yield 'Correct, allowed constructs, non-test methods, providers and helpers' => [
            'filePath' => __DIR__ . '/Fixture/Correct/allowedConstructs.php.inc',
        ];

        yield 'Correct, class without a Test or TestCase suffix and a function outside a class' => [
            'filePath' => __DIR__ . '/Fixture/Correct/nonTestClass.php.inc',
        ];

        yield 'Correct, test classes outside the test namespace' => [
            'filePath' => __DIR__ . '/Fixture/Correct/nonTestNamespace.php.inc',
        ];

        yield 'Correct, trait without Test in its name' => [
            'filePath' => __DIR__ . '/Fixture/Correct/nonTestTrait.php.inc',
        ];

        yield 'Correct, custom function imports and attribute names are not forbidden function calls' => [
            'filePath' => __DIR__ . '/Fixture/Correct/importedFunctionsAndAttributes.php.inc',
        ];

        yield 'Wrong, imported function aliases, including calls after attribute groups' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/importedFunctions.php.inc',
            'expectedErrors' => [
                ['line' => 16, 'code' => ForbiddenTestMethodConstructSniff::class . '.Sleep'],
                ['line' => 17, 'code' => ForbiddenTestMethodConstructSniff::class . '.Sleep'],
                ['line' => 18, 'code' => ForbiddenTestMethodConstructSniff::class . '.Random'],
                ['line' => 19, 'code' => ForbiddenTestMethodConstructSniff::class . '.Sleep'],
                ['line' => 25, 'code' => ForbiddenTestMethodConstructSniff::class . '.Sleep'],
            ],
        ];

        yield 'Wrong, all aliases in comma-separated function imports' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/multipleFunctionImports.php.inc',
            'expectedErrors' => [
                ['line' => 14, 'code' => ForbiddenTestMethodConstructSniff::class . '.Sleep'],
                ['line' => 15, 'code' => ForbiddenTestMethodConstructSniff::class . '.Random'],
                ['line' => 16, 'code' => ForbiddenTestMethodConstructSniff::class . '.Sleep'],
                ['line' => 19, 'code' => ForbiddenTestMethodConstructSniff::class . '.Sleep'],
            ],
        ];

        yield 'Wrong, function imports are limited to their namespace declaration' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/namespaceFunctionImports.php.inc',
            'expectedErrors' => [
                ['line' => 10, 'code' => ForbiddenTestMethodConstructSniff::class . '.Sleep'],
                ['line' => 34, 'code' => ForbiddenTestMethodConstructSniff::class . '.Sleep'],
                ['line' => 44, 'code' => ForbiddenTestMethodConstructSniff::class . '.Sleep'],
            ],
        ];

        yield 'Wrong, function imports do not leak between bracketed namespaces' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/bracketedNamespaceFunctionImports.php.inc',
            'expectedErrors' => [
                ['line' => 21, 'code' => ForbiddenTestMethodConstructSniff::class . '.Sleep'],
            ],
        ];

        yield 'Wrong, comments inside function imports' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/commentedFunctionImports.php.inc',
            'expectedErrors' => [
                ['line' => 16, 'code' => ForbiddenTestMethodConstructSniff::class . '.Random'],
                ['line' => 19, 'code' => ForbiddenTestMethodConstructSniff::class . '.Sleep'],
            ],
        ];

        yield 'Wrong, group function imports and imports after the class' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/groupAndLateFunctionImports.php.inc',
            'expectedErrors' => [
                ['line' => 18, 'code' => ForbiddenTestMethodConstructSniff::class . '.Random'],
            ],
        ];

        yield 'Wrong, if, switch and match in a test method' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/branching.php.inc',
            'expectedErrors' => [
                [
                    'line' => 12,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Branching',
                ],
                [
                    'line' => 15,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Branching',
                ],
                [
                    'line' => 19,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Branching',
                ],
            ],
        ];

        yield 'Wrong, ternary and short ternary in a test method and in anonymous class arguments' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/ternary.php.inc',
            'expectedErrors' => [
                [
                    'line' => 12,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Ternary',
                ],
                [
                    'line' => 13,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Ternary',
                ],
                [
                    'line' => 14,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Ternary',
                ],
            ],
        ];

        yield 'Wrong, null coalescing and nullsafe operators in a test method' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/coalesce.php.inc',
            'expectedErrors' => [
                [
                    'line' => 13,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Coalesce',
                ],
                [
                    'line' => 14,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Coalesce',
                ],
                [
                    'line' => 15,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Coalesce',
                ],
            ],
        ];

        yield 'Wrong, boolean operators in a test method' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/booleanLogic.php.inc',
            'expectedErrors' => [
                [
                    'line' => 12,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.BooleanLogic',
                ],
                [
                    'line' => 13,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.BooleanLogic',
                ],
                [
                    'line' => 14,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.BooleanLogic',
                ],
                [
                    'line' => 15,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.BooleanLogic',
                ],
                [
                    'line' => 16,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.BooleanLogic',
                ],
            ],
        ];

        yield 'Wrong, try/catch/finally in a test method' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/tryCatch.php.inc',
            'expectedErrors' => [
                [
                    'line' => 12,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.TryCatch',
                ],
            ],
        ];

        yield 'Wrong, expectException calls on self references, including the nullsafe operator' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/expectException.php.inc',
            'expectedErrors' => [
                [
                    'line' => 12,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.ExpectException',
                ],
                [
                    'line' => 13,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.ExpectException',
                ],
                [
                    'line' => 14,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.ExpectException',
                ],
                [
                    'line' => 15,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.ExpectException',
                ],
                [
                    'line' => 16,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Coalesce',
                ],
                [
                    'line' => 16,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.ExpectException',
                ],
                [
                    'line' => 17,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.ExpectException',
                ],
            ],
        ];

        yield 'Wrong, sleep and usleep calls' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/sleep.php.inc',
            'expectedErrors' => [
                [
                    'line' => 12,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Sleep',
                ],
                [
                    'line' => 13,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Sleep',
                ],
                [
                    'line' => 14,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Sleep',
                ],
                [
                    'line' => 15,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Sleep',
                ],
            ],
        ];

        yield 'Wrong, random function calls' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/random.php.inc',
            'expectedErrors' => [
                [
                    'line' => 13,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Random',
                ],
                [
                    'line' => 14,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Random',
                ],
                [
                    'line' => 15,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Random',
                ],
                [
                    'line' => 16,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Random',
                ],
                [
                    'line' => 17,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Random',
                ],
                [
                    'line' => 18,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Random',
                ],
                [
                    'line' => 19,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Random',
                ],
                [
                    'line' => 20,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Random',
                ],
                [
                    'line' => 21,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Random',
                ],
            ],
        ];

        yield 'Wrong, forbidden constructs inside a closure and an arrow function' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/nestedClosure.php.inc',
            'expectedErrors' => [
                [
                    'line' => 13,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Ternary',
                ],
                [
                    'line' => 15,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Coalesce',
                ],
            ],
        ];

        yield 'Wrong, test method in a trait with Test in its name' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/testTrait.php.inc',
            'expectedErrors' => [
                [
                    'line' => 12,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Branching',
                ],
                [
                    'line' => 19,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Ternary',
                ],
            ],
        ];

        yield 'Wrong, inherited test method in an abstract TestCase class' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/abstractTestCase.php.inc',
            'expectedErrors' => [
                [
                    'line' => 12,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Ternary',
                ],
            ],
        ];

        yield 'Wrong, private and protected helper methods' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/helperMethods.php.inc',
            'expectedErrors' => [
                [
                    'line' => 17,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Ternary',
                ],
                [
                    'line' => 22,
                    'code' => ForbiddenTestMethodConstructSniff::class . '.Ternary',
                ],
            ],
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }

    public function testItSucceedsWithDataProviderHintForRandomFunctions(): void
    {
        $filePath = __DIR__ . '/Fixture/Wrong/random.php.inc';

        $result = $this->sniffFileProcessor->processFile($filePath, new Configuration(isFixer: false));

        self::assertSame(
            \array_map(
                static fn(string $function): string => \sprintf(
                    'Test method must not call %s(). Add a data provider with one case for each value that matters.',
                    $function
                ),
                [
                    'mt_srand',
                    'srand',
                    'rand',
                    'mt_rand',
                    'random_int',
                    'lcg_value',
                    'array_rand',
                    'shuffle',
                    'str_shuffle',
                ]
            ),
            $this->extractErrorMessages($result)
        );
    }

    public function testItSucceedsWithFixHintInEachMessage(): void
    {
        $filePath = __DIR__ . '/Fixture/Wrong/allConstructsMessages.php.inc';

        $result = $this->sniffFileProcessor->processFile($filePath, new Configuration(isFixer: false));

        self::assertSame(
            [
                'Test method must not contain a null coalescing operator (??, ??=).'
                . ' Give each value explicitly in the data provider or the arrangement.',
                'Test method must not contain a nullsafe operator (?->).'
                . ' Set the value explicitly, or assert that it is not null before you use it.',
                'Test method must not contain a ternary operator. Give the expected value from the data provider.',
                'Test method must not contain branching (if, match, switch).'
                . ' Write one test for each path, or add a data provider with one case for each path.',
                'Test method must not call sleep().'
                . ' Move the time forward with a clock, for example CarbonImmutable::setTestNow().',
                'Test method must not call sleep() imported as pause().'
                . ' Move the time forward with a clock, for example CarbonImmutable::setTestNow().',
                'Test method must not contain boolean logic (&&, ||, and, or, xor).'
                . ' Use one assertion for each condition.',
                'Test method must not call expectException().'
                . ' Call the code in a closure and assert the thrown exception after the call.',
                'Test method must not contain try/catch/finally.'
                . ' Call the code in a closure and assert the thrown exception after the call.',
            ],
            $this->extractErrorMessages($result)
        );
    }

    /**
     * @param array{file_diffs?: \Symplify\EasyCodingStandard\ValueObject\Error\FileDiff[], coding_standard_errors?: \Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError[]} $result
     *
     * @return string[]
     */
    private function extractErrorMessages(array $result): array
    {
        $result += [Bridge::CODING_STANDARD_ERRORS => []];

        return \array_map(
            static fn(CodingStandardError $error): string => $error->getMessage(),
            $result[Bridge::CODING_STANDARD_ERRORS]
        );
    }
}
