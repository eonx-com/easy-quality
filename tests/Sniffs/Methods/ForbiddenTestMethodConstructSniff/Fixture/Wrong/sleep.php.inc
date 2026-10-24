<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class SleepTest extends TestCase
{
    public function testSleep(): void
    {
        \sleep(0);
        usleep(0);

        self::assertTrue(true);
    }
}
