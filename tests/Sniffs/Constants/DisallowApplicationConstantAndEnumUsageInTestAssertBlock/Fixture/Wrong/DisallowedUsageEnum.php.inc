<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Constants\DisallowApplicationConstantAndEnumUsageInTestAssertBlock\Fixture\Wrong;

use App\Enum\ApplicationEntity;

final class DisallowedUsageEnum
{
    public function testSomething()
    {
        $expectedResult = 'some-status';

        $actualResult = 2 + 2;

        self::assertSame(ApplicationEntity::SomeEnum->value, $actualResult);
    }
}
