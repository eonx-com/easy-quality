<?php
declare(strict_types=1);

class TestClass
{
    public const TEST = 'no-matter';
}

function someFunction(
    int $param1,
    array $param2,
    ?array $param3 = [],
    array $param4 = [],
    int $param5 = 0,
    $param6 = [],
    ?array $param7 = null,
    ?int $param8 = 123,
    ?string $param9 = '123',
    ?string $readonly = 'readonly',
    ?stdClass $class,
    string $const1 = PHP_EOL,
    string $const2 = PHP_EOL,
    string $const3 = TestClass::TEST
) {
    // No body needed
}
-----
<?php
declare(strict_types=1);

class TestClass
{
    public const TEST = 'no-matter';
}

function someFunction(
    int $param1,
    array $param2,
    ?array $param3 = null,
    ?array $param4 = null,
    ?int $param5 = null,
    $param6 = null,
    ?array $param7 = null,
    ?int $param8 = null,
    ?string $param9 = null,
    ?string $readonly = null,
    ?stdClass $class = null,
    ?string $const1 = null,
    ?string $const2 = null,
    ?string $const3 = null
) {
    // No body needed
}
