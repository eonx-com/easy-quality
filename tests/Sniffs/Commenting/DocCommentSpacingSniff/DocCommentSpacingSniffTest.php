<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Commenting\DocCommentSpacingSniff;

use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class DocCommentSpacingSniffTest extends AbstractCheckerTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    /**
     * @return mixed[]
     *
     * @see testProcessSucceeds
     */
    public function provideCorrectData(): array
    {
        return [
            [
                'filePath' => '/Fixtures/Correct/DocCommentSpacingSniffTest.php.inc',
            ],
        ];
    }

    /**
     * @return mixed[]
     *
     * @see testProcessFails
     */
    public function provideWrongData(): array
    {
        return [
            [
                'filePath' => '/Fixtures/Wrong/DocCommentSpacingSniffTest.php.inc',
            ],
        ];
    }

    /**
     * @dataProvider provideWrongData
     */
    public function testProcessFails(string $filePath): void
    {
        $wrongFileInfo = new SmartFileInfo(__DIR__ . $filePath);
        $this->doTestFileInfoWithErrorCountOf($wrongFileInfo, 0);
    }

    /**
     * @dataProvider provideCorrectData
     */
    public function testProcessSucceeds(string $filePath): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . $filePath);
        $this->doTestCorrectFileInfo($fileInfo);
    }
}
