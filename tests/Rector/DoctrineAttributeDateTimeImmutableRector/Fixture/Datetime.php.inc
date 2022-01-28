<?php
declare(strict_types=1);

namespace TestNamespace;

use Doctrine\ORM\Mapping as ORM;

class ApiResource
{
    #[ORM\Column(type: 'datetime')]
    protected $createdAt;
}
-----
<?php
declare(strict_types=1);

namespace TestNamespace;

use Doctrine\ORM\Mapping as ORM;

class ApiResource
{
    #[ORM\Column(type: 'datetime_immutable')]
    protected $createdAt;
}