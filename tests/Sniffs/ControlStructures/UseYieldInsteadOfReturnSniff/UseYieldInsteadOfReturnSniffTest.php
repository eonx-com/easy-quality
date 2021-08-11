<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff;

use EonX\EasyQuality\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \EonX\EasyQuality\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff
 *
 * @internal
 */
final class UseYieldInsteadOfReturnSniffTest extends AbstractCheckerTestCase
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
        return UseYieldInsteadOfReturnSniff::class;
    }

    protected function getCheckerConfiguration(): array
    {
        return [
            'applyTo' => [
                [
                    'namespace' => '/^EonX\\\EasyQuality\\\Tests\\\Sniffs\\\ControlStructures\\\UseYieldInsteadOfReturnSniff\\\Fixtures\\\(Correct|Wrong)$/',
                    'patterns' => ['/provide[A-Z]*/'],
                ],
            ],
        ];
    }
}
