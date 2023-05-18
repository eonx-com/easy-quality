<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Methods\TestMethodNameSniff\Fixture\Correct\AnotherNamespace;

final class NamespaceDoesNotHaveForbiddenPatterns
{
    public function testmethod(): void
    {
        // No body needed here
    }
}
