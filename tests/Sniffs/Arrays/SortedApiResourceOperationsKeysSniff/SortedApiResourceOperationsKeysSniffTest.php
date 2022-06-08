<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Arrays\SortedApiResourceOperationsKeysSniff;

use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \EonX\EasyQuality\Sniffs\Arrays\SortedApiResourceOperationsKeysSniff
 *
 * @internal
 */
final class SortedApiResourceOperationsKeysSniffTest extends AbstractCheckerTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    public function testProcessFails(): void
    {
        $wrongFileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Wrong.php.inc');
        $this->doTestFileInfo($wrongFileInfo);
    }

    public function testProcessSucceeds(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Correct.php.inc');
        $this->doTestCorrectFileInfo($fileInfo);
    }
}
