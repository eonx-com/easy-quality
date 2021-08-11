<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff\Fixtures\Correct;

final class MethodDoesNotHaveApplyToPatterns
{
    public function provideData(): iterable
    {
        return [1,2,3];
    }
}
