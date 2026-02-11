<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector\ValueObject;

use PHPStan\Type\ObjectType;

final readonly class PhpDocReturnForIterable
{
    public function __construct(private string $type, private string $method)
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
