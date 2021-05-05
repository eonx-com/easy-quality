<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ExplicitBoolCompareRector;

use EonX\EasyQuality\Rector\ExplicitBoolCompareRector;
use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \EonX\EasyQuality\Rector\StrictInArrayRector
 *
 * @internal
 */
final class ExplicitBoolCompareRectorTest extends AbstractRectorTestCase
{
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

    protected function getRectorClass(): string
    {
        return ExplicitBoolCompareRector::class;
    }
}
