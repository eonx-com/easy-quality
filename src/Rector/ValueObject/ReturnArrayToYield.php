<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector\ValueObject;

use PHPStan\Type\ObjectType;

final class ReturnArrayToYield
{
    public function __construct(private readonly string $type, private readonly string $method)
    {
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getObjectType(): ObjectType
    {
        return new ObjectType($this->type);
    }
}
