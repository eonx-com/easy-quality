<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\AddCoversAnnotationRector;

use EonX\EasyQuality\Rector\AddCoversAnnotationRector;
use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \EonX\EasyQuality\Rector\AddCoversAnnotationRector
 *
 * @internal
 */
final class AddCoversAnnotationRectorTest extends AbstractRectorTestCase
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

    /**
     * Returns Rector with configuration.
     *
     * @return mixed[]
     */
    protected function getRectorsWithConfiguration(): array
    {
        return [
            AddCoversAnnotationRector::class => [
                AddCoversAnnotationRector::REPLACE_ARRAY => ['Tests\\Unit\\'],
            ],
        ];
    }
}
