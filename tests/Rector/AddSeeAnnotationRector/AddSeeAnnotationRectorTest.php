<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\AddSeeAnnotationRector;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

/**
 * @covers \EonX\EasyQuality\Rector\AddSeeAnnotationRector
 *
 * @internal
 */
final class AddSeeAnnotationRectorTest extends AbstractRectorTestCase
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
        yield from $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    /**
     * @dataProvider provideData
     */
    public function testRule(string $fileInfo): void
    {
        $this->doTestFile($fileInfo);
    }
}
