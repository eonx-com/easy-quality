<?php
declare(strict_types=1);

namespace TestNamespace;

use DateTime;

final class SomeClass
{
    private DateTime $createdAt;

    public ?DateTime $updatedAt;
}
-----
<?php
declare(strict_types=1);

namespace TestNamespace;

use DateTime;

final class SomeClass
{
    private \DateTimeImmutable $createdAt;

    public ?\DateTimeImmutable $updatedAt;
}