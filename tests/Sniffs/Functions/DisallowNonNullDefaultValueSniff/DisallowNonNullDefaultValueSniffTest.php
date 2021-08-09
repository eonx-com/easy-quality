<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Functions\DisallowNonNullDefaultValueSniff;

use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \EonX\EasyQuality\Sniffs\Functions\DisallowNonNullDefaultValueSniff
 *
 * @internal
 */
final class DisallowNonNullDefaultValueSniffTest extends AbstractCheckerTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    /**
     * @throws \ECSPrefix20210805\Symplify\SmartFileSystem\Exception\FileNotFoundException
     * @throws \RectorPrefix20210706\Symplify\SmartFileSystem\Exception\FileNotFoundException
     */
    public function testProcessMultiLineParametersInClassMethodSucceeds(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Correct/ClassMethodMultiLineParameters.php.inc');
        $this->doTestCorrectFileInfo($fileInfo);
    }

    /**
     * @throws \ECSPrefix20210805\Symplify\SmartFileSystem\Exception\FileNotFoundException
     * @throws \RectorPrefix20210706\Symplify\SmartFileSystem\Exception\FileNotFoundException
     */
    public function testProcessMultiLineParametersInClosureSucceeds(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Correct/ClosureMultiLineParameters.php.inc');
        $this->doTestCorrectFileInfo($fileInfo);
    }

    /**
     * @throws \ECSPrefix20210805\Symplify\SmartFileSystem\Exception\FileNotFoundException
     * @throws \RectorPrefix20210706\Symplify\SmartFileSystem\Exception\FileNotFoundException
     */
    public function testProcessMultiLineParametersInSimpleFunctionSucceeds(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Correct/SimpleFunctionMultiLineParameters.php.inc');
        $this->doTestCorrectFileInfo($fileInfo);
    }

    /**
     * @throws \ECSPrefix20210805\Symplify\SmartFileSystem\Exception\FileNotFoundException
     * @throws \RectorPrefix20210706\Symplify\SmartFileSystem\Exception\FileNotFoundException
     */
    public function testProcessSingleLineParametersInClassMethodSucceeds(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Correct/ClassMethodSingleLineParameters.php.inc');
        $this->doTestCorrectFileInfo($fileInfo);
    }

    /**
     * @throws \ECSPrefix20210805\Symplify\SmartFileSystem\Exception\FileNotFoundException
     * @throws \RectorPrefix20210706\Symplify\SmartFileSystem\Exception\FileNotFoundException
     */
    public function testProcessSingleLineParametersInClosureSucceeds(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Correct/ClosureSingleLineParameters.php.inc');
        $this->doTestCorrectFileInfo($fileInfo);
    }

    /**
     * @throws \ECSPrefix20210805\Symplify\SmartFileSystem\Exception\FileNotFoundException
     * @throws \RectorPrefix20210706\Symplify\SmartFileSystem\Exception\FileNotFoundException
     */
    public function testProcessSingleLineParametersInSimpleFunctionSucceeds(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Correct/SimpleFunctionSingleLineParameters.php.inc');
        $this->doTestCorrectFileInfo($fileInfo);
    }

    /**
     * @throws \ECSPrefix20210805\Symplify\SmartFileSystem\Exception\FileNotFoundException
     * @throws \RectorPrefix20210706\Symplify\SmartFileSystem\Exception\FileNotFoundException
     */
    public function testProcessWrongMultiLineParametersInClassMethodFails(): void
    {
        $wrongFileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Wrong/ClassMethodMultiLineParameters.php.inc');
        $this->doTestFileInfoWithErrorCountOf($wrongFileInfo, 10);
    }

    /**
     * @throws \ECSPrefix20210805\Symplify\SmartFileSystem\Exception\FileNotFoundException
     * @throws \RectorPrefix20210706\Symplify\SmartFileSystem\Exception\FileNotFoundException
     */
    public function testProcessWrongMultiLineParametersInClosureFails(): void
    {
        $wrongFileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Wrong/ClosureMultiLineParameters.php.inc');
        $this->doTestFileInfoWithErrorCountOf($wrongFileInfo, 10);
    }

    /**
     * @throws \ECSPrefix20210805\Symplify\SmartFileSystem\Exception\FileNotFoundException
     * @throws \RectorPrefix20210706\Symplify\SmartFileSystem\Exception\FileNotFoundException
     */
    public function testProcessWrongMultiLineParametersInSimpleFunctionFails(): void
    {
        $wrongFileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Wrong/SimpleFunctionMultiLineParameters.php.inc');
        $this->doTestFileInfoWithErrorCountOf($wrongFileInfo, 10);
    }

    /**
     * @throws \ECSPrefix20210805\Symplify\SmartFileSystem\Exception\FileNotFoundException
     * @throws \RectorPrefix20210706\Symplify\SmartFileSystem\Exception\FileNotFoundException
     */
    public function testProcessWrongSingleLineParametersInClassMethodFails(): void
    {
        $wrongFileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Wrong/ClassMethodSingleLineParameters.php.inc');
        $this->doTestFileInfoWithErrorCountOf($wrongFileInfo, 3);
    }

    /**
     * @throws \ECSPrefix20210805\Symplify\SmartFileSystem\Exception\FileNotFoundException
     * @throws \RectorPrefix20210706\Symplify\SmartFileSystem\Exception\FileNotFoundException
     */
    public function testProcessWrongSingleLineParametersInClosureFails(): void
    {
        $wrongFileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Wrong/ClosureSingleLineParameters.php.inc');
        $this->doTestFileInfoWithErrorCountOf($wrongFileInfo, 3);
    }

    /**
     * @throws \ECSPrefix20210805\Symplify\SmartFileSystem\Exception\FileNotFoundException
     * @throws \RectorPrefix20210706\Symplify\SmartFileSystem\Exception\FileNotFoundException
     */
    public function testProcessWrongSingleLineParametersInSimpleFunctionFails(): void
    {
        $wrongFileInfo = new SmartFileInfo(__DIR__ . '/Fixtures/Wrong/SimpleFunctionSingleLineParameters.php.inc');
        $this->doTestFileInfoWithErrorCountOf($wrongFileInfo, 3);
    }

    // @todo Research issue `Error: Undefined constant 'T_CLOSE_SHORT_ARRAY'`
    protected function setUp(): void
    {
        $this->markTestSkipped();
    }
}
