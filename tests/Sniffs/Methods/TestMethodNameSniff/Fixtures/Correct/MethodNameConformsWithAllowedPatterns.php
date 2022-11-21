<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Methods\TestMethodNameSniff\Fixtures\Correct;

final class MethodNameConformsWithAllowedPatterns
{
    public function testCreateSucceeds(): void
    {
        // No body needed here
    }
}
