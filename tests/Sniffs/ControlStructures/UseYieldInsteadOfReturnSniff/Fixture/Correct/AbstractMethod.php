<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff\Fixture\Correct;

abstract class AbstractMethod
{
    abstract public function provideData(): iterable;
}
