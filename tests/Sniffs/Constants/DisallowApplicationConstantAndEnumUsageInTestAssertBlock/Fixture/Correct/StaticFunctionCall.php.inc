<?php
declare(strict_types=1);

namespace App\Tests\Quality\Ecs\DisallowApplicationConstantAndEnumUsageInTestAssertBlock\Correct;

use App\Entities\ApplicationEntity;

final class TestClass
{
    public function testSomething()
    {
        $expectedResult = 'some-status';

        $actualResult = 2 + 2;

        self::assertSame($expectedResult, $actualResult);
        self::assertInstanceOf(ApplicationEntity::staticMethodCall());
    }
}
