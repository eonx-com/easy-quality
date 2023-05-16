<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Methods\TestMethodNameSniff\Fixture\Wrong;

final class NotAllowedMethodName
{
    public function testmethod(): void
    {
        // No body needed here
    }
}
