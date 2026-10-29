<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

function testHelper(): int
{
    return \time() > 0 ? 1 : 0;
}

final class TestDataBuilder
{
    public function testValue(): int
    {
        return \time() > 0 ? 1 : 0;
    }
}
