<?php

declare (strict_types=1);

namespace EonX\EasyQuality\Rector\EasyRankeable;

use EonX\EasyQuality\Rector\Order\Contract\RankeableInterface;
use PhpParser\Node\Expr\ArrayItem;

final class ApiResourceOperationEasyRankeable implements RankeableInterface
{
    private ArrayItem $item;

    public function __construct(ArrayItem $item)
    {
        $this->item = $item;
    }

    public function getName(): string
    {
        return $this->item->key->value;
    }

    /**
     * An array to sort the element order by
     *
     * @return bool[]|int[]
     */
    public function getRanks(): array
    {
        return [
            $this->getName() !== 'get',
            $this->getName() !== 'post',
            $this->getName() !== 'put',
            $this->getName() !== 'patch',
            $this->getName() !== 'delete',
            $this->getName() !== 'activate',
            $this->getName() !== 'deactivate',
            $this->getName(),
        ];
    }
}
