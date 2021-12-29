<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector\EasyRankeable;

use PhpParser\Node\Stmt\ClassConst;
use Rector\Order\Contract\RankeableInterface;

final class ClassConstEasyRankeable implements RankeableInterface
{
    /**
     * @var \PhpParser\Node\Stmt\ClassConst
     */
    private $classConst;

    public function __construct(ClassConst $classConst)
    {
        $this->classConst = $classConst;
    }
    public function getName(): string
    {
        return $this->classConst->consts[0]->name->toString();
    }

    public function getRanks(): array
    {
        return [
            $this->getName(),
        ];
    }
}
