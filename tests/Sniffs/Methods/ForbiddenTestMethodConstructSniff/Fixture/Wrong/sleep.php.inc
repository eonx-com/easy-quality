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
        \time_nanosleep(0, 0);
        time_sleep_until(\microtime(true));

        self::assertTrue(true);
    }
}
