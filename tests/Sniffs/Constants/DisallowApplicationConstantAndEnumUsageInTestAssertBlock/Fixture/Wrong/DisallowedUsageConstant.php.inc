<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Constants\DisallowApplicationConstantAndEnumUsageInTestAssertBlock\Fixture\Wrong;

use App\Entities\ApplicationEntity;

final class TestClass
{
    public function testSomething()
    {
        $expectedResult = 'some-status';

        $actualResult = 2 + 2;

        self::assertSame(ApplicationEntity::SOME_CONSTANT, $actualResult);
    }
}
