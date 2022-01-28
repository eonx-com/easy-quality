<?php
declare(strict_types=1);

namespace TestNamespace;

final class SomeClass
{
    private CarbonImmutable $createdAt;

    public ?CarbonImmutable $updatedAt;
}
-----
<?php
declare(strict_types=1);

namespace TestNamespace;

final class SomeClass
{
    private CarbonImmutable $createdAt;

    public ?CarbonImmutable $updatedAt;
}