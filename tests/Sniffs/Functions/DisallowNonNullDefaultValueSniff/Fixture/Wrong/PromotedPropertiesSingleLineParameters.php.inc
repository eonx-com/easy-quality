<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Functions\DisallowNonNullDefaultValueSniff\Fixture\Correct;

final class TestClass
{
    public function __construct(private int $param1, private ?array $param3 = null, private ?int $param4)
    {
        // No body needed
    }
}
-----
<?php

declare(strict_types = 1);

namespace EonX\EasyQuality\Tests\Sniffs\Functions\DisallowNonNullDefaultValueSniff\Fixture\Correct;

final class TestClass
{
    public function __construct(private int $param1, private ?array $param3 = null, private ?int $param4 = null)
    {
        // No body needed
    }
}