<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff;

use EonX\EasyQuality\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class UseYieldInsteadOfReturnSniffTest extends AbstractSniffTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }

    /**
     * @inheritDoc
     */
    public static function provideFixtures(): iterable
    {
        yield 'Wrong, use return in method' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/UseReturnInMethod.php',
            'expectedErrors' => [
                [
                    'line' => 12,
                    'code' => UseYieldInsteadOfReturnSniff::class . '.UsingYieldInsteadReturn',
                ],
                [
                    'line' => 17,
                    'code' => UseYieldInsteadOfReturnSniff::class . '.UsingYieldInsteadReturn',
                ],
            ],
        ];

        yield 'Correct, use yield in method' => [
            'filePath' => __DIR__ . '/Fixture/Correct/UseYieldInMethod.php',
        ];

        yield 'Correct, use return array in method' => [
            'filePath' => __DIR__ . '/Fixture/Correct/UseReturnArrayInMethod.php',
        ];

        yield 'Correct, namespace does not have apply to patterns' => [
            'filePath' => __DIR__ . '/Fixture/Correct/OtherNamespace/NamespaceDoesNotHaveApplyToPatterns.php',
        ];
    }
}
