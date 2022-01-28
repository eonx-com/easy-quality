<?php

declare (strict_types=1);

namespace EonX\EasyQuality\Rector\EasyRankeable;

use EonX\EasyQuality\Rector\Order\Contract\RankeableInterface;
use PhpParser\Node\Stmt\ClassMethod;

final class ClassMethodEasyRankeable implements RankeableInterface
{
    /**
     * @var \PhpParser\Node\Stmt\ClassMethod
     */
    private $classMethod;

    public function __construct(ClassMethod $classMethod)
    {
        $this->classMethod = $classMethod;
    }

    public function getName(): string
    {
        return $this->classMethod->name->toString();
    }

    /**
     * An array to sort the element order by
     *
     * @return bool[]|int[]
     */
    public function getRanks(): array
    {
        return [
            $this->getName() !== '__construct',
            $this->getVisibilityLevelOrder(),
            !$this->classMethod->isAbstract(),
            !$this->classMethod->isStatic(),
            $this->getName(),
        ];
    }

    private function getVisibilityLevelOrder(): int
    {
        if ($this->classMethod->isPrivate()) {
            return 2;
        }

        return (int)$this->classMethod->isProtected();
    }
}
