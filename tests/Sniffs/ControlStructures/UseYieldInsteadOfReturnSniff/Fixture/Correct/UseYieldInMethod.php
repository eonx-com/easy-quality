<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff\Fixture\Correct;

final class UseYieldInMethod
{
    public function provideData(): iterable
    {
        $array = [1, 2, 3];

        yield $array;
    }
}
