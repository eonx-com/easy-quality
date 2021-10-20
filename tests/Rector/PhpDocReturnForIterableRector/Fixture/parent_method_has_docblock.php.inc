<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

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
        yield ['<?php implode("", $foo, );', '<?php implode($foo, "", );'];
    }
}

?>
-----
<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

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
        yield ['<?php implode("", $foo, );', '<?php implode($foo, "", );'];
    }
}

?>
