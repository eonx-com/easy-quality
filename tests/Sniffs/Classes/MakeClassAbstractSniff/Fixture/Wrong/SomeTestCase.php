<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\MakeClassAbstractSniff\Fixture\Wrong;

final class SomeTestCase
{
    // No body needed
}
-----
<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\MakeClassAbstractSniff\Fixture\Wrong;

abstract class SomeTestCase
{
    // No body needed
}
