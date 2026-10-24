<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class CoalesceTest extends TestCase
{
    public function testCoalesce(): void
    {
        $values = ['a' => null];
        $first = $values['a'] ?? 1;
        $values['b'] ??= 2;
        $third = $values['a']?->shuffle();

        self::assertSame([$first, $values['b']], [1, $third]);
    }
}
