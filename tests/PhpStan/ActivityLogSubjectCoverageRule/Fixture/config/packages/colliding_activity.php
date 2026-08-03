<?php
declare(strict_types=1);

use EonX\EasyQuality\Tests\PhpStan\ActivityLogSubjectCoverageRule\Stub\NotAssertedSubjectStub;
use EonX\EasyQuality\Tests\PhpStan\ActivityLogSubjectCoverageRule\Stub\SubNotAssertedSubjectStub;

return [
    'easy_activity' => [
        'subjects' => [
            // The short name of the subject below is a suffix of this one
            SubNotAssertedSubjectStub::class => [
                'type' => 'SubNotAssertedSubjectStub',
            ],
            NotAssertedSubjectStub::class => [
                'type' => 'NotAssertedSubjectStub',
            ],
        ],
    ],
];
