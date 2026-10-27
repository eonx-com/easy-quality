<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use function sleep as pause;
use function usleep as microPause;
use function random_int as randomNumber;
use function App\Clock\pause as sleep;

final class ImportedFunctionsTest extends TestCase
{
    public function testImportedFunctions(): void
    {
        pause(1);
        MICROPAUSE(1);
        randomNumber(1, 10);
        \sleep(1);
    }

    public function testCallsAfterAttributes(): void
    {
        $callback = #[Marker, Sleep(1)] static function (): void {
            pause(1);
        };
    }
}
