<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PhpStan\WebhookEventCoverageRule\Fixture;

use EonX\EasyQuality\Tests\PhpStan\WebhookEventCoverageRule\Stub\EntityExpectationStub;
use EonX\EasyQuality\Tests\PhpStan\WebhookEventCoverageRule\Stub\WebhookStub;

final class AssertsSomeEvents
{
    public function testWebhooks(): void
    {
        $this->findOneEntity(WebhookStub::class, [
            'event' => 'asserted:same_call',
        ]);

        $this->assertEntity(WebhookStub::class)
            ->toBeInDb([
                'event' => 'asserted:chain',
            ]);

        // Asserting the absence of a webhook does not cover its event
        $this->assertEntity(WebhookStub::class)
            ->toNotBeInDb([
                'event' => 'not_asserted:negatively',
            ]);

        // The same criteria key of another entity does not cover the event either
        $this->findOneEntity(\stdClass::class, [
            'event' => 'not_asserted:other_entity',
        ]);
    }

    private function assertEntity(string $entityClass): EntityExpectationStub
    {
        return new EntityExpectationStub();
    }

    /**
     * @param array<string, string> $criteria
     */
    private function findOneEntity(string $entityClass, array $criteria): void
    {
        // Stands in for the entity finder of the project under analysis
    }
}
