<?php
declare(strict_types=1);

use EonX\EasyQuality\Tests\PhpStan\ThrowExceptionMessageRule\Stub\SomeException;

throw SomeException::create('some-message');
