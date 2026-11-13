<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use function sleep as pause;

final class AllConstructsMessagesTest extends TestCase
{
    public function testAllConstructs(): void
    {
        $values = ['status' => null];
        $status = $values['status'] ?? 'active';
        $date = $this->createDate()?->format('Y-m-d');
        $expected = $status === 'active' ? 1 : 0;
        if ($expected === 1) {
            \sleep(1);
            pause(1);
        }
        $isValid = $expected > 0 && $date !== null;
        $this->expectException(\RuntimeException::class);
        try {
            self::assertTrue($isValid);
        } catch (\Throwable) {
            self::fail();
        }
    }

    private function createDate(): ?\DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
