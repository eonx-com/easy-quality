<?php
declare(strict_types=1);

namespace TestNamespace;

use Carbon\Carbon;

final class SomeClass
{
    private Carbon $createdAt;

    protected ?Carbon $updatedAt;
}
-----
<?php
declare(strict_types=1);

namespace TestNamespace;

use Carbon\Carbon;

final class SomeClass
{
    private \Carbon\CarbonImmutable $createdAt;

    protected ?\Carbon\CarbonImmutable $updatedAt;
}