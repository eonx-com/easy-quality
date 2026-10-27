<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class BooleanLogicTest extends TestCase
{
    public function testBooleanLogic(): void
    {
        $first = \time() > 0 && \time() > 1;
        $second = \time() > 0 || \time() > 1;
        $third = (\time() > 0 and \time() > 1);
        $fourth = (\time() > 0 or \time() > 1);
        $fifth = (\time() > 0 xor \time() > 1);

        self::assertSame([$first, $second, $third], [$fourth, $fifth, true]);
    }
}
