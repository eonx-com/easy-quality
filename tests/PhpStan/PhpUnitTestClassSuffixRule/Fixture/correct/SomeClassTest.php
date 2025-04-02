<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PhpStan\PhpUnitTestClassSuffixRule\Fixture\correct;

use PHPUnit\Framework\TestCase;

final class SomeClassTest extends TestCase
{
    public function testSomething(): void
    {
        $class = new class () {
        };

        self::assertIsObject($class);
    }
}