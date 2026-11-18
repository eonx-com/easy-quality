<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class RandomTest extends TestCase
{
    public function testRandom(): void
    {
        $values = [1, 2];
        \mt_srand(1);
        \srand(1);
        $first = \rand();
        $second = \mt_rand();
        $third = \random_int(0, 1);
        $fourth = \lcg_value();
        $fifth = \array_rand($values);
        \shuffle($values);
        $sixth = \str_shuffle('ab');

        self::assertNotSame([$first, $second, $third], [$fourth, $fifth, $sixth]);
    }
}
