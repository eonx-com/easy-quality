<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\PhpDocCommentRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

/**
 * @covers \EonX\EasyQuality\Rector\PhpDocCommentRector
 *
 * @internal
 */
final class PhpDocCommentRectorTest extends AbstractRectorTestCase
{
    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    /**
     * @return Iterator<string>
     */
    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    /**
     * @dataProvider provideData()
     */
    public function testRule(string $fileInfo): void
    {
        $this->doTestFile($fileInfo);
    }
}
