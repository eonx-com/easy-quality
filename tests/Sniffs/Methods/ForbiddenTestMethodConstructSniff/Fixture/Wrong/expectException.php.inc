<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class ExpectExceptionTest extends TestCase
{
    public function testExpectException(): void
    {
        $this->expectException(\RuntimeException::class);
        self::expectExceptionMessage('message');
        static::expectExceptionCode(1);
        parent::expectExceptionMessageMatches('/message/');
        $this?->expectExceptionObject(new \RuntimeException('message', 1));
        $this::expectExceptionCode(1);
        $this->expectExceptionToBeLogged();

        throw new \RuntimeException('message', 1);
    }
}
