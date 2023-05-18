<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Namespaces\Psr4Sniff;

use EonX\EasyQuality\Sniffs\Namespaces\Psr4Sniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class Psr4SniffTest extends AbstractSniffTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }

    /**
     * @see testFile
     *
     * @inheritDoc
     */
    public function provideFixtures(): iterable
    {
        yield 'Correct, PSR-4' => [
            'filePath' => __DIR__ . '/Fixture/Correct/ValidPsr4.php.inc',
        ];

        yield 'Wrong, not PSR-4' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/NotPsr4.php.inc',
            'expectedErrors' => [
                [
                    'line' => 3,
                    'code' => Psr4Sniff::class . '.PSR4Namespace',
                ],
            ],
        ];
    }

    /**
     * @param array<int, array{line: int, code: string}>|null $expectedErrors
     *
     * @dataProvider provideFixtures
     */
    public function testFile(string $filePath, ?array $expectedErrors = null): void
    {
        self::assertNotEmpty($this->sniffFileProcessor->getCheckers());
        $this->checkSniffErrors($filePath, $expectedErrors);
    }
}
