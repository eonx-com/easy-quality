<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;


use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;

abstract class AbstractParentTestCase extends ParentTestCase
{
    /**
     * @return iterable<mixed>
     */
    abstract public static function provideData(): iterable;
}


class WithParentTestCase extends AbstractParentTestCase
{
    public static function provideData(): array
    {
        return ['value 1', 'value 2'];
    }
}

?>
-----
<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;

abstract class AbstractParentTestCase extends ParentTestCase
{
    /**
     * @return iterable<mixed>
     */
    abstract public static function provideData(): iterable;
}


class WithParentTestCase extends AbstractParentTestCase
{
    public static function provideData(): iterable
    {
        yield 'value 1';
        yield 'value 2';
    }
}

?>
