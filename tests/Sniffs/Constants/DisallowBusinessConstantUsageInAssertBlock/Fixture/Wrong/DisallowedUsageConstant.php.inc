<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Constants\DisallowBusinessConstantUsageInAssertBlock\Fixture\Wrong;

use App\Entities\BusinessEntity;

final class TestClass
{
    public function testSomething()
    {
        $expectedResult = 'some-status';

        $actualResult = 2 + 2;

        self::assertSame(BusinessEntity::SOME_CONSTANT, $actualResult);
    }
}
