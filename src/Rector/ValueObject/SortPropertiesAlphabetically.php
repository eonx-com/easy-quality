<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector\ValueObject;

final class SortPropertiesAlphabetically
{
    /**
     * @var string
     */
    private $rankeableClass;

    public function __construct(string $rankeableClass)
    {
        $this->rankeableClass = $rankeableClass;
    }

    public function getRankeableCLass(): string
    {
        return $this->rankeableClass;
    }
}
