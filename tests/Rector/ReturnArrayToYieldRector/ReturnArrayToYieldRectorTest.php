<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

/**
 * @covers \EonX\EasyQuality\Rector\ReturnArrayToYieldRector
 *
 * @internal
 */
final class ReturnArrayToYieldRectorTest extends AbstractRectorTestCase
{
    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    public function provideData(): iterable
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $fileInfo): void
    {
        $this->doTestFile($fileInfo);
    }
}
