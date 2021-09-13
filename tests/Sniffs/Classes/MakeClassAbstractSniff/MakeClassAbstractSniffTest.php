<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\MakeClassAbstractSniff;

use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \EonX\EasyQuality\Sniffs\Classes\MakeClassAbstractSniff
 *
 * @internal
 */
final class MakeClassAbstractSniffTest extends AbstractCheckerTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    public function testProcessIfClassNotAbstract(): void
    {
        $wrongFileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Wrong/SomeTestCase.php');
        $this->doTestFileInfoWithErrorCountOf($wrongFileInfo, 0);
    }

    public function testProcessSucceedsIfClassNameDoesNotNotHaveApplyToPatterns(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Correct/ClassNameDoesNotNotHaveApplyToPatterns.php');
        $this->doTestCorrectFileInfo($fileInfo);
    }

    public function testProcessSucceedsIfAbstractClass(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Correct/AbstractClass.php');
        $this->doTestCorrectFileInfo($fileInfo);
    }

    public function testProcessSucceedsIfNamespaceDoesNotHaveApplyToPatterns(): void
    {
        $fileInfo = new SmartFileInfo(
            __DIR__ . '/Fixtures/Correct/AnotherNamespace/NamespaceDoesNotHaveApplyToPatterns.php'
        );
        $this->doTestCorrectFileInfo($fileInfo);
    }
}
