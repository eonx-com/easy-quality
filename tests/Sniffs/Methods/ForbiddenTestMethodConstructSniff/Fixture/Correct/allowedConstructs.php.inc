<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class AllowedConstructsTest extends TestCase
{
    public function nonTestMethod(): int
    {
        return \time() > 0 ? 1 : 0;
    }

    public function TestUppercasePrefix(): int
    {
        return \time() > 0 ? 1 : 0;
    }

    public function testAnonymousClass(): void
    {
        $object = new class {
            public function testValue(): int
            {
                return \time() > 0 ? 1 : 0;
            }
        };

        self::assertSame(1, $object->testValue());
    }

    public function testLoops(): void
    {
        $sum = 0;
        for ($index = 0; $index < 2; $index++) {
            $sum += $index;
        }
        foreach ([1, 2] as $value) {
            $sum += $value;
        }
        while ($sum < 10) {
            $sum++;
        }

        self::assertSame(10, $sum);
    }

    public function testMethodsNamedLikeForbiddenFunctions(): void
    {
        $object = new Helper();

        self::assertSame(
            [$object->sleep(), $object->expectException(), Helper::expectException(), Helper::rand()],
            \array_fill(0, 4, 1)
        );
    }

    public function testNamesThatAreNotForbiddenFunctionCalls(): void
    {
        function rand(): int
        {
            return 1;
        }

        $factory = static fn (): self => expectException();

        self::assertInstanceOf(Sleep::class, new Sleep());
        self::assertSame(\Foo\rand(), $factory());
    }

    public function testOpaqueRandomValues(): void
    {
        self::assertSame(16, \strlen(\random_bytes(16)));
        self::assertNotSame('', \uniqid());
    }
}
