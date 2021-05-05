<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\NoNotOperatorSniff;

use EonX\EasyQuality\Sniffs\ControlStructures\NoNotOperatorSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NoNotOperatorSniffTest extends AbstractCheckerTestCase
{
    public function testSniff(): void
    {
        $wrongFileInfo = new SmartFileInfo(__DIR__ . '/Fixture/NoNotOperatorSniffTest.php.inc');
        $this->doTestFileInfoWithErrorCountOf($wrongFileInfo, 1);
    }

    protected function getCheckerClass(): string
    {
        return NoNotOperatorSniff::class;
    }
}
