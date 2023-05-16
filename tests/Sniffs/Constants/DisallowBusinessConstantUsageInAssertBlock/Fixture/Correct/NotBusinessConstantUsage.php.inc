<?php
declare(strict_types=1);

namespace App\Tests\Quality\Ecs\DisallowBusinessConstantUsageInAssertBlock\Correct;

use Symfony\Component\HttpFoundation\Response;

final class TestClass
{
    private const ALLOWED_CONST = 'some-value';

    public function testSomething()
    {
        $expectedResult = 'some-status';

        $actualResult = 2 + 2;

        self::assertSame(Response::HTTP_CREATED, $actualResult);
    }
}
