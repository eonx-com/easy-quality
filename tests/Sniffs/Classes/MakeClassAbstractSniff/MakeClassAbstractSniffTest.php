<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\MakeClassAbstractSniff;

use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class MakeClassAbstractSniffTest extends AbstractSniffTestCase
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
        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/SomeTestCase.php',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/AbstractClass.php',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/ClassNameDoesNotHaveApplyToPatterns.php',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/AnotherNamespace/NamespaceDoesNotHaveApplyToPatterns.php',
        ];
    }
}
