<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\RestoreDefaultNullToNullableTypeParameterRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

/**
 * @covers \EonX\EasyQuality\Rector\RestoreDefaultNullToNullableTypeParameterRector
 *
 * @internal
 */
final class RestoreDefaultNullToNullableTypeParameterRectorTest extends AbstractRectorTestCase
{
    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    /**
     * @return \Iterator<\Symplify\SmartFileSystem\SmartFileInfo>
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
