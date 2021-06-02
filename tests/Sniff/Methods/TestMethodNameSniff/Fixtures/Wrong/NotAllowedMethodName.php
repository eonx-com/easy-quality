<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniff\Methods\TestMethodNameSniff\Fixtures\Wrong;

final class NotAllowedMethodName
{
    public function testmethod(): void
    {
        // No body needed here
    }
}
