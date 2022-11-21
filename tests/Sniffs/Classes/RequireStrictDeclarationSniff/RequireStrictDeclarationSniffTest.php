<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\RequireStrictDeclarationSniff;

use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class RequireStrictDeclarationSniffTest extends AbstractCheckerTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    /**
     * @return iterable<array<int, (\Symplify\SmartFileSystem\SmartFileInfo|int)>>
     */
    public function providerTestSniff(): iterable
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/RequireStrictDeclarationSniffTest.php.inc'), 1];
    }

    /**
     * @dataProvider providerTestSniff
     */
    public function testSniff(SmartFileInfo $smartFileInfo, int $expectedErrorCount): void
    {
        $this->doTestFileInfoWithErrorCountOf($smartFileInfo, $expectedErrorCount);
    }
}
