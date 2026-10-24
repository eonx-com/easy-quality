<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

trait ValueTrait
{
    public function testValue(): int
    {
        return \time() > 0 ? 1 : 0;
    }
}
