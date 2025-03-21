<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\PhpStan\ClassInheritanceRule\Fixture\correct;

use EonX\EasyQuality\Tests\PhpStan\ClassInheritanceRule\Stub\SomeAbstractClass;
use EonX\EasyQuality\Tests\PhpStan\ClassInheritanceRule\Stub\SomeInterface;

final class FixtureImplementsInterfaceAndExtendClassCase3 extends SomeAbstractClass implements SomeInterface
{
}