<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

/**
 * @covers \EonX\EasyQuality\Rector\PhpDocReturnForIterableRector
 *
 * @internal
 */
final class PhpDocReturnForIterableRectorTest extends AbstractRectorTestCase
{
    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    /**
     * @return iterable<string, array{filePath: string}>
     *
     * @see testRule
     */
    public function provideData(): iterable
    {
        foreach ($this->yieldFilesFromDirectory(__DIR__ . '/Fixture') as $filePath) {
            $filePath = \strval($filePath);

            yield $filePath => [
                'filePath' => $filePath,
            ];
        }
    }

    /**
     * @dataProvider provideData
     */
    public function testRule(string $filePath): void
    {
        $this->doTestFile($filePath);
    }
}
