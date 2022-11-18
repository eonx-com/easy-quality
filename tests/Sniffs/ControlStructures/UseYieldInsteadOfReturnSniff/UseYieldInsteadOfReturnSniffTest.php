<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff;

use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \EonX\EasyQuality\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff
 *
 * @internal
 */
final class UseYieldInsteadOfReturnSniffTest extends AbstractCheckerTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    public function testProcessIfMethodUseReturn(): void
    {
        $wrongFileInfo = new SmartFileInfo(__DIR__ . '/Fixture/Wrong/UseReturnInMethod.php');
        $this->doTestFileInfoWithErrorCountOf($wrongFileInfo, 2);
    }

    public function testProcessSucceedsIfMethodUseReturnArray(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixture/Correct/UseReturnArrayInMethod.php');
        $this->doTestCorrectFileInfo($fileInfo);
    }

    public function testProcessSucceedsIfMethodUseYield(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixture/Correct/UseYieldInMethod.php');
        $this->doTestCorrectFileInfo($fileInfo);
    }

    public function testProcessSucceedsIfNamespaceDoesNotHaveApplyToPatterns(): void
    {
        $fileInfo = new SmartFileInfo(
            __DIR__ . '/Fixture/Correct/OtherNamespace/NamespaceDoesNotHaveApplyToPatterns.php'
        );
        $this->doTestCorrectFileInfo($fileInfo);
    }
}
