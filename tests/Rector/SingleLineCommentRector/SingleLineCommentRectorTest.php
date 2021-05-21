<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\SingleLineCommentRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \EonX\EasyQuality\Rector\SingleLineCommentRector
 *
 * @internal
 */
final class SingleLineCommentRectorTest extends AbstractRectorTestCase
{
    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    /**
     * @return Iterator<\Symplify\SmartFileSystem\SmartFileInfo>
     */
    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    /**
     * @dataProvider provideData()
     */
    public function testRule(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }
}
