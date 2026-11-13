<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class NestedClosureTest extends TestCase
{
    public function testNestedClosure(): void
    {
        $closure = static function (int $value): int {
            return $value > 0 ? 1 : 0;
        };
        $arrowFunction = static fn (?int $value): int => $value ?? 0;

        self::assertSame($closure(1), $arrowFunction(1));
    }
}
