<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff\Fixtures\Correct;

final class UseReturnArrayInMethod
{
    public function arrangeSomething(): iterable
    {
        $array = [1, 2, 3];

        return $array;
    }
}
