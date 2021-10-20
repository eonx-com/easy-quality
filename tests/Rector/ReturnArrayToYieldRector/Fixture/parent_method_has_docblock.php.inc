<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

abstract class AbstractParentTestCase{
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

abstract class AbstractParentTestCase{
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
