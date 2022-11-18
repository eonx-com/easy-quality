<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff\Fixture\Correct;

final class UseReturnArrayInMethod
{
    public function provideData(): iterable
    {
        return [1, 2, 3];
    }
}
