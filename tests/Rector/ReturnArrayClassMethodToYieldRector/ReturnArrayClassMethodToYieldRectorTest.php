<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector;

use Rector\CodingStyle\Rector\ClassMethod\ReturnArrayClassMethodToYieldRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \EonX\EasyQuality\Rector\ReturnArrayClassMethodToYieldRector
 *
 * @internal
 */
final class ReturnArrayClassMethodToYieldRectorTest extends AbstractRectorTestCase
{
    public function provideData(): iterable
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    protected function provideConfigFileInfo(): SmartFileInfo
    {
        return new SmartFileInfo(__DIR__ . '/config.php');
    }
}