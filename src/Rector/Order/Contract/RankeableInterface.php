<?php
declare (strict_types=1);

namespace EonX\EasyQuality\Rector\Order\Contract;

interface RankeableInterface
{
    public function getName(): string;

    /**
     * @return bool[]|int[]
     */
    public function getRanks(): array;
}
