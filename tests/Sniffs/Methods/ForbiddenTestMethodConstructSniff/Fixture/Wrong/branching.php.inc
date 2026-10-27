<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class BranchingTest extends TestCase
{
    public function testBranching(): void
    {
        if (\time() > 0) {
            self::assertTrue(true);
        }
        switch (\time()) {
            default:
                self::assertTrue(true);
        }
        $value = match (true) {
            default => 1,
        };

        self::assertSame(1, $value);
    }
}
