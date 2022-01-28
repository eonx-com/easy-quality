<?php
declare(strict_types=1);

namespace TestNamespace;

final class SomeClass
{
    private DateTimeImmutable $createdAt;

    protected ?DateTimeImmutable $updatedAt;
}
-----
<?php
declare(strict_types=1);

namespace TestNamespace;

final class SomeClass
{
    private DateTimeImmutable $createdAt;

    protected ?DateTimeImmutable $updatedAt;
}