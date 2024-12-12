<?php
declare(strict_types=1);

use EonX\EasyQuality\Tests\PHPStan\ThrowExceptionMessageRule\Stub\SomeException;

throw SomeException::create('some-message');
