<?php
declare(strict_types=1);

namespace App\Tests\Quality\Ecs\DisallowApplicationConstantAndEnumUsageInTestAssertBlock\Correct;

use Symfony\Component\HttpFoundation\Response;

final class TestClass
{
    public function testSomething()
    {
        $actualResult = 2 + 2;

        self::assertSame(Response::HTTP_CREATED, $actualResult);
    }
}
