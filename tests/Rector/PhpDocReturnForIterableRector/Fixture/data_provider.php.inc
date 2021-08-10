<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

class DataProvider extends ParentTestCase
{
    /**
     * @return mixed[]
     *
     * some comment
     */
    public static function provideData1(): iterable
    {
        yield ['<?php implode("", $foo, );', '<?php implode($foo, "", );'];
    }

    /**
     * @return array[]
     *
     * @see testSomething
     */
    public static function provideData2(): iterable
    {
        yield ['<?php implode("", $foo, );', '<?php implode($foo, "", );'];
    }

    /**
     * @return mixed[]
     */
    public static function dataProvider(): iterable
    {
        yield ['<?php implode(\'\', $foo, );', '<?php implode($foo, );'];
        yield ['<?php implode(\'\', $foo, );', '<?php implode($foo, );'];
    }
}

?>
-----
<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

class DataProvider extends ParentTestCase
{
    /**
     * @return iterable<mixed>
     *
     * some comment
     */
    public static function provideData1(): iterable
    {
        yield ['<?php implode("", $foo, );', '<?php implode($foo, "", );'];
    }

    /**
     * @return iterable<mixed>
     *
     * @see testSomething
     */
    public static function provideData2(): iterable
    {
        yield ['<?php implode("", $foo, );', '<?php implode($foo, "", );'];
    }

    /**
     * @return iterable<mixed>
     */
    public static function dataProvider(): iterable
    {
        yield ['<?php implode(\'\', $foo, );', '<?php implode($foo, );'];
        yield ['<?php implode(\'\', $foo, );', '<?php implode($foo, );'];
    }
}

?>
