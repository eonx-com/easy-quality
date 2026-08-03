<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PhpStan\ActivityLogSubjectCoverageRule\Fixture;

use EonX\EasyQuality\Tests\PhpStan\ActivityLogSubjectCoverageRule\Stub\ActivityLogAssertionStub;
use EonX\EasyQuality\Tests\PhpStan\ActivityLogSubjectCoverageRule\Stub\AssertedSubjectStub;

final class AssertsOneSubject
{
    public function assertSubject(): void
    {
        $assertion = new ActivityLogAssertionStub();

        $assertion->assertActivityLog(action: 'create', subject: new AssertedSubjectStub());
    }
}