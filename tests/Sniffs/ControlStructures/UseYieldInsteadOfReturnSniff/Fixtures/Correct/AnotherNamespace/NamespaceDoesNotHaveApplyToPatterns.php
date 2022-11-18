<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff\Fixtures\Correct\AnotherNamespace;

final class NamespaceDoesNotHaveApplyToPatterns
{
    public function provideData(): iterable
    {
        $array = [1, 2, 3];

        return $array;
    }
}
