<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

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
        yield ['<?php implode("", $foo, );', '<?php implode($foo, "", );'];
    }
}

?>
-----
<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

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
        yield ['<?php implode("", $foo, );', '<?php implode($foo, "", );'];
    }
}

?>
