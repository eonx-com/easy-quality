<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Attributes\DoctrineColumnTypeSniff;

use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class DoctrineColumnTypeSniffTest extends AbstractSniffTestCase
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
            'filePath' => __DIR__ . '/Fixture/Correct/correct.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/wrong.php.inc',
        ];
    }
}
