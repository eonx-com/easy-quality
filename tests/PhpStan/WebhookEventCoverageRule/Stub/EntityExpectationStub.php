<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PhpStan\WebhookEventCoverageRule\Stub;

/**
 * Stands in for the fluent entity assertion of the project under analysis.
 */
final class EntityExpectationStub
{
    /**
     * @param array<string, string> $criteria
     */
    public function toBeInDb(array $criteria): self
    {
        return $this;
    }

    /**
     * @param array<string, string> $criteria
     */
    public function toNotBeInDb(array $criteria): self
    {
        return $this;
    }
}
