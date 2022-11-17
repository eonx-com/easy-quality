<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Attributes\SortAttributesAlphabeticallySniff;

use EonX\EasyQuality\Sniffs\Attributes\SortAttributesAlphabeticallySniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class SortAttributesAlphabeticallySniffTest extends AbstractSniffTestCase
{
    protected static function getSniffClassName(): string
    {
        return SortAttributesAlphabeticallySniff::class;
    }

    public function testNoErrors(): void
    {
        $report = self::checkFile(__DIR__ . '/Fixtures/Correct/correct.php');

        self::assertNoSniffErrorInFile($report);
    }

    public function testErrors(): void
    {
        $report = self::checkFile(__DIR__ . '/Fixtures/Wrong/wrong.php');

        self::assertSame(3, $report->getErrorCount());
        self::assertSniffError($report, 3, SortAttributesAlphabeticallySniff::CODE_INCORRECT_ORDER);
        self::assertSniffError($report, 12, SortAttributesAlphabeticallySniff::CODE_INCORRECT_ORDER);
        self::assertSniffError($report, 18, SortAttributesAlphabeticallySniff::CODE_INCORRECT_ORDER);
        self::assertAllFixedInFile($report);
    }
}
