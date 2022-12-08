<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\UselessSingleAnnotationRector;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

/**
 * @covers \EonX\EasyQuality\Rector\UselessSingleAnnotationRector
 *
 * @internal
 */
final class UselessSingleAnnotationRectorTest extends AbstractRectorTestCase
{
    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    /**
     * @return iterable<string>
     *
     * @see testRule
     */
    public function provideData(): iterable
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    /**
     * @dataProvider provideData
     */
    public function testRule(string $fileInfo): void
    {
        $this->doTestFile($fileInfo);
    }
}
