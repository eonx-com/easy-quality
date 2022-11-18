<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Namespaces\Psr4Sniff;

use EonX\EasyQuality\Sniffs\Namespaces\Psr4Sniff;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class Psr4SniffTest extends AbstractCheckerTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    /**
     * @return iterable<array<int, SmartFileInfo|int>>
     */
    public function providerTestSniff(): iterable
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/NotPsr4.php.inc'), 1];

        yield [new SmartFileInfo(__DIR__ . '/Fixture/EonX/EasyQuality/Tests/ValidPsr4.php.inc'), 0];
    }

    /**
     * @dataProvider providerTestSniff()
     */
    public function testSniff(SmartFileInfo $smartFileInfo, int $expectedErrorCount): void
    {
        $this->doTestFileInfoWithErrorCountOf($smartFileInfo, $expectedErrorCount);
    }

    protected function getCheckerClass(): string
    {
        return Psr4Sniff::class;
    }
}
