<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Functions\DisallowNonNullDefaultValueSniff\Fixture\Wrong;

use stdClass;

final class TestClass
{
    /**
     * @var string
     */
    private const CONST = 'const';

    public function someMethod(int $param1, ?stdClass $class, string $const3 = self::CONST, array $param4 = []): void
    {
        // No body needed
    }
}
-----
<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Functions\DisallowNonNullDefaultValueSniff\Fixture\Wrong;

use stdClass;

final class TestClass
{
    /**
     * @var string
     */
    private const CONST = 'const';

    public function someMethod(int $param1, ?stdClass $class = null, ?string $const3 = null, ?array $param4 = null): void
    {
        // No body needed
    }
}
