<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\PropertyTypeSniff;

use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class PropertyTypeSniffTest extends AbstractCheckerTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    /**
     * @return iterable<string[]>
     */
    public function provideCorrectFixtures(): iterable
    {
        yield [
            'filePath' => '/Fixtures/Correct/correct.php.inc',
        ];
    }

    /**
     * @return iterable<string[]>
     */
    public function provideWrongFixtures(): iterable
    {
        yield [
            'filePath' => '/Fixtures/Wrong/wrong.php.inc',
        ];
    }

    /**
     * @dataProvider provideCorrectFixtures
     */
    public function testCorrectSniffs(string $filePath): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . $filePath);
        $this->doTestCorrectFileInfo($fileInfo);
    }

    /**
     * @dataProvider provideWrongFixtures
     */
    public function testWrongSniffs(string $filePath): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . $filePath);
        $this->doTestFileInfoWithErrorCountOf($fileInfo, 0);
    }
}
