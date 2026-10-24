<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class CustomPrefixTest extends TestCase
{
    public function itChecksValue(): void
    {
        self::assertSame(1, \time() > 0 ? 1 : 0);
    }
}
