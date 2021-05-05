<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Functions\DisallowNonNullDefaultValueSniff\Fixtures\Correct;

use stdClass;

final class TestClass
{
    public function someMethod(int $param1, $param2 = null, ?stdClass $class = null): void
    {
        // No body needed
    }
}
