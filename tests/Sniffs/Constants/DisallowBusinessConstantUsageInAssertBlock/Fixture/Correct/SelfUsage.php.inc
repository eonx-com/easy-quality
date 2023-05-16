<?php
declare(strict_types=1);

namespace App\Tests\Quality\Ecs\DisallowBusinessConstantUsageInAssertBlock\Correct;

final class TestClass
{
    private const ALLOWED_CONST = 'some-value';

    public function testSomething()
    {
        $expectedResult = 'some-status';

        $actualResult = 2 + 2;

        self::assertSame(self::ALLOWED_CONST, $actualResult);
    }
}
