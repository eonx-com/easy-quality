<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\RepositorySniff\Fixture\Wrong\Entity;

#[ORM\Entity(
    repositoryClass: SomeRepository::class
)]
#[ORM\Table(name: 'some')]
class Some extends AbstractDatabaseApiResource
{
}
