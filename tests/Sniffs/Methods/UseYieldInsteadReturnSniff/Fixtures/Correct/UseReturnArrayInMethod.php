<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Methods\UseYieldInsteadReturnSniff\Fixtures\Correct;

final class UseReturnArrayInMethod
{
    public function arrangeSomething(): iterable
    {
        $array = [1,2,3];

        return $array;
    }
}
