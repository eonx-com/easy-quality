<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Constants\DisallowBusinessConstantUsageInAssertBlock;

use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \EonX\EasyQuality\Sniffs\Constants\DisallowBusinessConstantUsageInAssertBlock
 *
 * @internal
 */
final class DisallowBusinessConstantUsageInAssertBlockTest extends AbstractCheckerTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    public function provideCorrectCases(): iterable
    {
        yield 'class usage' => [
            'filePath' => '/Fixture/Correct/ClassUsage.php.inc',
        ];
        yield 'static function call' => [
            'filePath' => '/Fixture/Correct/StaticFunctionCall.php.inc',
        ];
        yield 'not business constant usage' => [
            'filePath' => '/Fixture/Correct/NotBusinessConstantUsage.php.inc',
        ];
        yield 'self usage' => [
            'filePath' => '/Fixture/Correct/SelfUsage.php.inc',
        ];
    }

    public function provideWrongCases(): iterable
    {
        yield 'business constant usage' => [
            'filePath' => '/Fixture/Wrong/DisallowedUsageConstant.php.inc',
        ];
        yield 'business enum usage' => [
            'filePath' => '/Fixture/Wrong/DisallowedUsageEnum.php.inc',
        ];
    }

    /**
     * @dataProvider provideCorrectCases
     */
    public function testCorrectCases(string $filePath): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . $filePath);
        $this->doTestCorrectFileInfo($fileInfo);
    }

    /**
     * @dataProvider provideWrongCases
     */
    public function testWrongCases(string $filePath): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . $filePath);
        $this->doTestFileInfoWithErrorCountOf($fileInfo, 1);
    }
}
