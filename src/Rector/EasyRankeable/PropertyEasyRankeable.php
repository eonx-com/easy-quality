<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector\EasyRankeable;

use PhpParser\Node\Stmt\Property;
use Rector\Order\Contract\RankeableInterface;

final class PropertyEasyRankeable implements RankeableInterface
{
    /**
     * @var \PhpParser\Node\Stmt\Property
     */
    private $property;

    public function __construct(Property $property)
    {
        $this->property = $property;
    }
    public function getName(): string
    {
        return $this->property->props[0]->name->toString();
    }

    public function getRanks(): array
    {
        return [
            $this->getVisibilityLevelOrder(),
            !$this->property->isStatic(),
            $this->getName(),
        ];
    }

    private function getVisibilityLevelOrder() : int
    {
        if ($this->property->isPrivate()) {
            return 2;
        }

        return (int)$this->property->isProtected();
    }
}
