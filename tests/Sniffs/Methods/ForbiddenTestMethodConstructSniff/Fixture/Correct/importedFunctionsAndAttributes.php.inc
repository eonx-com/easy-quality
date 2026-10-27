<?php
declare(strict_types=1);

namespace App\Tests\Unit;

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
}
