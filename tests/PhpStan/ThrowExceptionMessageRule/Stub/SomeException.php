<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PhpStan\ThrowExceptionMessageRule\Stub;

use Exception;

final class SomeException extends Exception
{
    public static function create(string $message): self
    {
        return new self($message);
    }
}
