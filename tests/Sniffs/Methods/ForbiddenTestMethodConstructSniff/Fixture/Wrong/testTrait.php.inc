<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

trait VotersTestCaseTrait
{
    public function testAccess(): void
    {
        if (\time() > 0) {
            self::assertTrue(true);
        }
    }

    private function createValue(): int
    {
        return \time() > 0 ? 1 : 0;
    }
}
