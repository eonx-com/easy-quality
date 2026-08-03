<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PhpStan\WebhookEventCoverageRule\Stub;

enum WebhookEventStub: string
{
    case AssertedInChain = 'asserted:chain';

    case AssertedInSameCall = 'asserted:same_call';

    case NotAsserted = 'not_asserted:plain';

    case NotAssertedForOtherEntity = 'not_asserted:other_entity';

    case NotAssertedNegatively = 'not_asserted:negatively';
}
