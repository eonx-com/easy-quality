<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PhpStan\ActivityLogSubjectCoverageRule\Stub;

final class ActivityLogAssertionStub
{
    public function assertActivityLog(string $action, object $subject): void
    {
        // Intentionally empty, the rule only looks at the call site
    }
}
