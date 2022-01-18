<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector\ValueObject;

final class SortMethodsAlphabetically
{
    /**
     * @var string
     */
    private $rankeableClass;

    public function __construct(string $rankeableClass)
    {
        $this->rankeableClass = $rankeableClass;
    }

    public function getRankeableClass(): string
    {
        return $this->rankeableClass;
    }
}
