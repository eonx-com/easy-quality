<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\RepositorySniff\Fixture\Wrong;

final class SomeClass
{
    public function getRepository(string $repositoryClass): RepositoryInterface
    {
        return EntityManager::getRepository($repositoryClass);
    }
}