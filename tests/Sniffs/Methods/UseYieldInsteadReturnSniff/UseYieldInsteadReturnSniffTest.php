<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Methods\UseYieldInsteadReturnSniff;

use EonX\EasyQuality\Sniffs\Methods\UseYieldInsteadReturnSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \EonX\EasyQuality\Sniffs\Methods\TestMethodNameSniff
 *
 * @internal
 */
final class UseYieldInsteadReturnSniffTest extends AbstractCheckerTestCase
{
    public function testProcessIfMethodUseReturn(): void
    {
        $wrongFileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Wrong/UseReturnInMethod.php');
        $this->doTestFileInfoWithErrorCountOf($wrongFileInfo, 2);
    }

    public function testProcessSucceedsIfMethodUseReturnArray(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Correct/UseReturnArrayInMethod.php');
        $this->doTestCorrectFileInfo($fileInfo);
    }

    public function testProcessSucceedsIfMethodUseYield(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Correct/UseYieldInMethod.php');
        $this->doTestCorrectFileInfo($fileInfo);
    }

    public function testProcessSucceedsIfNamespaceDoesNotHaveApplyToPatterns(): void
    {
        $fileInfo = new SmartFileInfo(
            __DIR__ . '/Fixtures/Correct/AnotherNamespace/NamespaceDoesNotHaveApplyToPatterns.php'
        );
        $this->doTestCorrectFileInfo($fileInfo);
    }

    protected function getCheckerClass(): string
    {
        return UseYieldInsteadReturnSniff::class;
    }

    protected function getCheckerConfiguration(): array
    {
        return [
            'applyTo' => [
                [
                    'namespace' => '/^EonX\\\EasyQuality\\\Tests\\\Sniffs\\\Methods\\\UseYieldInsteadReturnSniff\\\Fixtures\\\(Correct|Wrong)$/',
                    'patterns' => ['/provide[A-Z]*/'],
                ],
            ],
        ];
    }
}
