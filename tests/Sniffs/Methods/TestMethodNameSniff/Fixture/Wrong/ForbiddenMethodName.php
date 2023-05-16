<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Methods\TestMethodNameSniff\Fixture\Wrong;

final class ForbiddenMethodName
{
    public function testCreateSucceedWithSomething(): void
    {
        // No body needed here
    }
}
