<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\RepositorySniff\Fixture\Correct\Entity;

#[ORM\Entity]
#[ORM\Table(name: 'some')]
class Some extends AbstractDatabaseApiResource
{
}
