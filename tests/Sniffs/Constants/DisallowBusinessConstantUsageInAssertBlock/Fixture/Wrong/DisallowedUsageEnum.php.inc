<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Constants\DisallowBusinessConstantUsageInAssertBlock\Fixture\Wrong;

use App\Enum\BusinessEnum;

final class DisallowedUsageEnum
{
    public function testSomething()
    {
        $expectedResult = 'some-status';

        $actualResult = 2 + 2;

        self::assertSame(BusinessEnum::SomeEnum->value, $actualResult);
    }
}
