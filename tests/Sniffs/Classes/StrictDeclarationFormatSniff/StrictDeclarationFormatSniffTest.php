<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\StrictDeclarationFormatSniff;

use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class StrictDeclarationFormatSniffTest extends AbstractCheckerTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    /**
     * @return iterable<array<int, (\Symplify\SmartFileSystem\SmartFileInfo|int)>>
     *
     * @see testSniff
     */
    public function providerTestSniff(): iterable
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/StrictDeclarationFormatSniffTest_ExtraLine.php.inc'), 1];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/StrictDeclarationFormatSniffTest_SameLine.php.inc'), 2];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/StrictDeclarationFormatSniffTest_WrongFormat.php.inc'), 1];
    }

    /**
     * @dataProvider providerTestSniff
     */
    public function testSniff(SmartFileInfo $smartFileInfo, int $expectedErrorCount): void
    {
        $this->doTestFileInfoWithErrorCountOf($smartFileInfo, $expectedErrorCount);
    }
}
