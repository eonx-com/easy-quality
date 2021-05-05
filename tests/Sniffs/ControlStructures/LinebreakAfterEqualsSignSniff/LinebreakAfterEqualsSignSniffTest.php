<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\LinebreakAfterEqualsSignSniff;

use EonX\EasyQuality\Sniffs\ControlStructures\LinebreakAfterEqualsSignSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \EonX\EasyQuality\Sniffs\ControlStructures\LinebreakAfterEqualsSignSniff
 *
 * @internal
 */
final class LinebreakAfterEqualsSignSniffTest extends AbstractCheckerTestCase
{
    /**
     * @return mixed[]
     *
     * @see testSniff
     */
    public function providerTestSniff(): iterable
    {
        yield [
            'filePath' => '/Fixture/LinebreakAfterEqualsSignSniffTest.php.inc',
            'expectedErrorCount' => 1,
        ];
    }

    /**
     * @param string $filePath
     *
     * @throws \Symplify\SmartFileSystem\Exception\FileNotFoundException
     *
     * @dataProvider providerTestSniff
     */
    public function testSniff(string $filePath, int $expectedErrorCount): void
    {
        $smartFileInfo = new SmartFileInfo(__DIR__ . $filePath);
        $this->doTestFileInfoWithErrorCountOf($smartFileInfo, $expectedErrorCount);
    }

    protected function getCheckerClass(): string
    {
        return LinebreakAfterEqualsSignSniff::class;
    }
}
