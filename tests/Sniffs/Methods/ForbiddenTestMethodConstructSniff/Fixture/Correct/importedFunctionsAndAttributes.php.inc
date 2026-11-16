<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Attribute\Test;
use PHPUnit\Framework\TestCase;
use function App\Clock\pause as sleep;
use function sleep as pause;

final class ImportedFunctionsAndAttributesTest extends TestCase
{
    public function testImportedCustomFunctions(): void
    {
        sleep();
        $clock->pause();
        Clock::pause();
    }

    public function testAttributeGroups(): void
    {
        $callback = #[Marker, Usleep(1)] static fn (): int => 1;
    }

    #[Test]
    public function itUsesUnrelatedImportedTestAttribute(): void
    {
        $value = \time() > 0 ? 1 : 0;
    }

    #[\App\Attribute\Test]
    public function itUsesUnrelatedFullyQualifiedTestAttribute(): void
    {
        $value = \time() > 0 ? 1 : 0;
    }
}
