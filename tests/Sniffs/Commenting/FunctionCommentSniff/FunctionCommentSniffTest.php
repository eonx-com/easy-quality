<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Commenting\FunctionCommentSniff;

use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \EonX\EasyQuality\Sniffs\Commenting\FunctionCommentSniff
 */
final class FunctionCommentSniffTest extends AbstractCheckerTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    /**
     * @return iterable<\Symplify\SmartFileSystem\SmartFileInfo[]>
     */
    public function provideCorrectFixtures(): iterable
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/Correct/correct.php.inc')];
    }

    /**
     * @return iterable<array<int, (\Symplify\SmartFileSystem\SmartFileInfo|int)>>
     */
    public function provideWrongFixtures(): iterable
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/Wrong/missingDocComment.php.inc'), 1];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/Wrong/incorrectCommentStyle.php.inc'), 1];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/Wrong/blankLineAfterComment.php.inc'), 2];
        yield [new SmartFileInfo(__DIR__ . '/Fixture/Wrong/missingParamDocComment.php.inc'), 1];
    }

    /**
     * @dataProvider provideCorrectFixtures
     */
    public function testCorrectSniffs(SmartFileInfo $fileInfo): void
    {
        // Loading classes from fixture for correct use `\class_exists()` and `\interface_exists()`
        require_once $fileInfo->getRealPath();

        $this->doTestCorrectFileInfo($fileInfo);
    }

    /**
     * @dataProvider provideWrongFixtures
     */
    public function testWrongSniffs(SmartFileInfo $wrongFileInfo, int $expectedErrorCount): void
    {
        $this->doTestFileInfoWithErrorCountOf($wrongFileInfo, $expectedErrorCount);
    }
}
