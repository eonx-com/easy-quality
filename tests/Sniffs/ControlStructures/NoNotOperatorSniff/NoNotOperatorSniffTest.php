<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\NoNotOperatorSniff;

use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class NoNotOperatorSniffTest extends AbstractCheckerTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    public function testSniff(): void
    {
        $wrongFileInfo = new SmartFileInfo(__DIR__ . '/Fixture/NoNotOperatorSniffTest.php.inc');
        $this->doTestFileInfoWithErrorCountOf($wrongFileInfo, 1);
    }
}
