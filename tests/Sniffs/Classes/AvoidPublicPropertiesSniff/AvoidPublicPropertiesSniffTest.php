<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\AvoidPublicPropertiesSniff;

use EonX\EasyQuality\Sniffs\Classes\AvoidPublicPropertiesSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AvoidPublicPropertiesSniffTest extends AbstractCheckerTestCase
{
    /**
     * @return iterable<array<int, SmartFileInfo|int>>
     */
    public function providerTestSniff(): iterable
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/AvoidPublicPropertiesSniffTest.php.inc'), 1];
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
        return AvoidPublicPropertiesSniff::class;
    }
}
