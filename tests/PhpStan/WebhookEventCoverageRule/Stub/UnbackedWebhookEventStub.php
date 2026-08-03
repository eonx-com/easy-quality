<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PhpStan\WebhookEventCoverageRule\Stub;

/**
 * Stands in for an event enum that lost its backing type.
 */
enum UnbackedWebhookEventStub
{
    case AssertedInChain;
}
