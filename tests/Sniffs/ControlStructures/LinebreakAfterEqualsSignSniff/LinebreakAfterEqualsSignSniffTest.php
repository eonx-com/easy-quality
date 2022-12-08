<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\LinebreakAfterEqualsSignSniff;

use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \EonX\EasyQuality\Sniffs\ControlStructures\LinebreakAfterEqualsSignSniff
 *
 * @internal
 */
final class LinebreakAfterEqualsSignSniffTest extends AbstractCheckerTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    /**
     * @return mixed[]
     *
     * @see testSniff
     */
    public function providerTestSniff(): iterable
    {
        yield [
            'expectedErrorCount' => 1,
            'filePath' => '/Fixture/LinebreakAfterEqualsSignSniffTest.php.inc',
        ];
    }

    /**
     * @dataProvider providerTestSniff
     */
    public function testSniff(int $expectedErrorCount, string $filePath): void
    {
        $smartFileInfo = new SmartFileInfo(__DIR__ . $filePath);
        $this->doTestFileInfoWithErrorCountOf($smartFileInfo, $expectedErrorCount);
    }
}
