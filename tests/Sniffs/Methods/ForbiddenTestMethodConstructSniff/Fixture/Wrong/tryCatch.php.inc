<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class TryCatchTest extends TestCase
{
    public function testTryCatch(): void
    {
        try {
            self::assertTrue(true);
        } catch (\Throwable) {
            self::fail();
        } finally {
            self::assertTrue(true);
        }
    }
}
