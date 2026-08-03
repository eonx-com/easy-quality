<?php
declare(strict_types=1);

use EonX\EasyQuality\Tests\PhpStan\ActivityLogSubjectCoverageRule\Stub\AssertedSubjectStub;
use EonX\EasyQuality\Tests\PhpStan\ActivityLogSubjectCoverageRule\Stub\NotAssertedSubjectStub;

return [
    'easy_activity' => [
        'subjects' => [
            AssertedSubjectStub::class => [
                'type' => 'AssertedSubjectStub',
            ],
            NotAssertedSubjectStub::class => [
                'type' => 'NotAssertedSubjectStub',
            ],
        ],
    ],
];
