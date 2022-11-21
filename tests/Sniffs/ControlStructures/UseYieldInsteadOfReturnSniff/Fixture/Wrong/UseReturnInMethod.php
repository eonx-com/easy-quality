<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff\Fixture\Wrong;

final class UseReturnInMethod
{
    public function provideData1(): iterable
    {
        $array = [1, 2, 3];

        return $array;
    }

    public function provideData2(): string
    {
        return 'string';
    }
}
