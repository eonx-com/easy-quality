<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Attributes\SortAttributesAlphabeticallySniff;

use EonX\EasyQuality\Sniffs\Attributes\SortAttributesAlphabeticallySniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

/**
 * @extends \EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase<\EonX\EasyQuality\Sniffs\Attributes\SortAttributesAlphabeticallySniff>
 */
final class SortAttributesAlphabeticallySniffTest extends AbstractSniffTestCase
{
    public function testErrors(): void
    {
        $report = self::checkFile(__DIR__ . '/Fixture/Wrong/wrong.php');

        self::assertSame(3, $report->getErrorCount());
        self::assertSniffError($report, 3, 'IncorrectOrder');
        self::assertSniffError($report, 12, 'IncorrectOrder');
        self::assertSniffError($report, 18, 'IncorrectOrder');
        self::assertAllFixedInFile($report);
    }

    public function testNoErrors(): void
    {
        $report = self::checkFile(__DIR__ . '/Fixture/Correct/correct.php');

        self::assertNoSniffErrorInFile($report);
    }

    protected static function getSniffClassName(): string
    {
        return SortAttributesAlphabeticallySniff::class;
    }
}
