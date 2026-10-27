<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class TernaryTest extends TestCase
{
    public function testTernary(): void
    {
        $first = \time() > 0 ? 1 : 0;
        $second = \time() ?: 1;
        $third = new class(\time() ?: 1) {
        };

        self::assertSame([$first, $second], [1, $third]);
    }
}
