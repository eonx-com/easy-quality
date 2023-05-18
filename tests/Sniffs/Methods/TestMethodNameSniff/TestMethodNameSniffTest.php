<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Methods\TestMethodNameSniff;

use EonX\EasyQuality\Sniffs\Methods\TestMethodNameSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class TestMethodNameSniffTest extends AbstractSniffTestCase
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
        yield 'Correct, ignored method name' => [
            'filePath' => __DIR__ . '/Fixture/Correct/IgnoredMethodName.php',
        ];

        yield 'Correct, method name conforms with allowed patterns' => [
            'filePath' => __DIR__ . '/Fixture/Correct/MethodNameConformsWithAllowedPatterns.php',
        ];

        yield 'Correct, method name does not conform with forbidden patterns' => [
            'filePath' => __DIR__ . '/Fixture/Correct/MethodNameDoesNotConformWithForbiddenPatterns.php',
        ];

        yield 'Correct, another namespace, namespace does not have forbidden patterns' => [
            'filePath' => __DIR__ . '/Fixture/Correct/AnotherNamespace/NamespaceDoesNotHaveForbiddenPatterns.php',
        ];

        yield 'Correct, another namespace, namespace does not have allowed patterns' => [
            'filePath' => __DIR__ . '/Fixture/Correct/AnotherNamespace/NamespaceDoesNotHaveAllowedPatterns.php',
        ];

        yield 'Wrong, forbidden method name' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/ForbiddenMethodName.php',
            'expectedErrors' => [
                [
                    'line' => 8,
                    'code' => TestMethodNameSniff::class . '.TestMethodNameSniff',
                ],
                [
                    'line' => 8,
                    'code' => TestMethodNameSniff::class . '.TestMethodNameSniff',
                ],
            ],
        ];

        yield 'Wrong, not allowed method name' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/NotAllowedMethodName.php',
            'expectedErrors' => [
                [
                    'line' => 8,
                    'code' => TestMethodNameSniff::class . '.TestMethodNameSniff',
                ],
                [
                    'line' => 8,
                    'code' => TestMethodNameSniff::class . '.TestMethodNameSniff',
                ],
            ],
        ];
    }
}
