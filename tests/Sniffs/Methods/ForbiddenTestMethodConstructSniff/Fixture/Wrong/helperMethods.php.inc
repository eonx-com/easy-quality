<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class HelperMethodsTest extends TestCase
{
    public static function provideValues(): iterable
    {
        yield [\time() > 0 ? 1 : 0];
    }

    protected function setUp(): void
    {
        $this->value = \time() > 0 ? 1 : 0;
    }

    private function createValue(): int
    {
        return \time() > 0 ? 1 : 0;
    }
}
