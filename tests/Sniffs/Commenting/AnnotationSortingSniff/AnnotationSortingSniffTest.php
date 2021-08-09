<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Commenting\AnnotationSortingSniff;

use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class AnnotationSortingSniffTest extends AbstractCheckerTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    /**
     * @return iterable<array<int, SmartFileInfo|int>>
     *
     * @see testSniff
     */
    public function providerTestSniff(): iterable
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/AnnotationSortingSniffTest.php.inc'), 1];
    }

    /**
     * @dataProvider providerTestSniff
     */
    public function testSniff(SmartFileInfo $smartFileInfo): void
    {
        $this->doTestFileInfoWithErrorCountOf($smartFileInfo, 1);
    }
}
