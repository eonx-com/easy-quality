<?php
declare(strict_types=1);

class TestClass
{
    public const TEST = 'no-matter';
}

$ext1 = 'no-matter';

(static function (int $param1, ?stdClass $class, string $const3 = TestClass::TEST, array $param4 = []) use ($ext1) {
    // No body needed
})->call(new \stdClass());
-----
<?php
declare(strict_types=1);

class TestClass
{
    public const TEST = 'no-matter';
}

$ext1 = 'no-matter';

(static function (int $param1, ?stdClass $class = null, ?string $const3 = null, ?array $param4 = null) use ($ext1) {
    // No body needed
})->call(new \stdClass());
