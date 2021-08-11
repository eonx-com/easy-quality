<?php

declare(strict_types=1);

namespace EonX\EasyQuality\ValueObject;

use PHPStan\Type\ObjectType;

final class ReturnArrayToYield
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $type;

    public function __construct(string $type, string $method)
    {
        $this->type = $type;
        $this->method = $method;
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
