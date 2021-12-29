<?php

declare (strict_types=1);

namespace EonX\EasyQuality\Rector\EasyRankeable;

use Closure;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Order\Contract\RankeableInterface;

final class ClassMethodEasyRankeable implements RankeableInterface
{
    /**
     * @var \PhpParser\Node\Stmt\ClassMethod
     */
    private $classMethod;

    /**
     * @var Closure
     */
    private $rankeableCallable;

    public function __construct(ClassMethod $classMethod, ?Closure $rankeableCallable = null)
    {
        $this->classMethod = $classMethod;
        $this->rankeableCallable = $rankeableCallable ?? Closure::fromCallable([$this, 'getDefaultRankeableCallable']);
    }

    private function getDefaultRankeableCallable(): array
    {
        return [
            $this->getName() !== '__construct',
            $this->getVisibilityLevelOrder(),
            !$this->classMethod->isAbstract(),
            !$this->classMethod->isStatic(),
            $this->getName(),
        ];
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
        return ($this->rankeableCallable)();
    }

    private function getVisibilityLevelOrder(): int
    {
        if ($this->classMethod->isPrivate()) {
            return 2;
        }

        return (int)$this->classMethod->isProtected();
    }
}
