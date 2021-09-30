<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

abstract class AbstractParentTestCase{
    /**
     * @return iterable<mixed>
     */
    abstract public static function provideData1(): iterable;
}


class DataProvider extends AbstractParentTestCase
{
    public static function provideData1(): iterable
    {
        yield ['<?php implode("", $foo, );', '<?php implode($foo, "", );'];
    }
}

?>
-----
<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

abstract class AbstractParentTestCase{
    /**
     * @return iterable<mixed>
     */
    abstract public static function provideData1(): iterable;
}


class DataProvider extends AbstractParentTestCase
{
    public static function provideData1(): iterable
    {
        yield ['<?php implode("", $foo, );', '<?php implode($foo, "", );'];
    }
}

?>
