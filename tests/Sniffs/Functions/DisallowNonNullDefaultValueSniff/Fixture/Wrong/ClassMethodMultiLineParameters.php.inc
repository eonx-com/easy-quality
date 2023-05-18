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

    public function someMethod(
        int $param1,
        array $param2,
        ?array $param3 = [],
        array $param4 = [],
        int $param5 = 0,
        $param6 = [],
        ?array $param7 = null,
        ?int $param8 = 123,
        ?string $param9 = '123',
        ?stdClass $class,
        string $const1 = PHP_EOL,
        string $const2 = \PHP_EOL,
        string $const3 = self::CONST
    ): void {
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

    public function someMethod(
        int $param1,
        array $param2,
        ?array $param3 = null,
        ?array $param4 = null,
        ?int $param5 = null,
        $param6 = null,
        ?array $param7 = null,
        ?int $param8 = null,
        ?string $param9 = null,
        ?stdClass $class = null,
        ?string $const1 = null,
        ?string $const2 = null,
        ?string $const3 = null
    ): void {
        // No body needed
    }
}
