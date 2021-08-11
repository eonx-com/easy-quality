<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;

class DataProvider extends ParentTestCase
{
    public static function provideData1()
    {
        //some comment
        return [
            ['<?php implode("", $foo, );', '<?php implode($foo, "", );']
        ];
    }

    /**
     * @return array[]
     *
     * @see testSomething
     */
    public static function provideData2()
    {
        return [
            ['<?php implode("", $foo, );', '<?php implode($foo, "", );']
        ];
    }

    public static function provideData3()
    {
        $array = [1, 2, 3];

        return $array;
    }

    public static function dataProvider()
    {
        return [
            ['<?php implode(\'\', $foo, );', '<?php implode($foo, );'],
            ['<?php implode(\'\', $foo, );', '<?php implode($foo, );']
        ];
    }
}

?>
-----
<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;

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

    public static function provideData3()
    {
        $array = [1, 2, 3];

        return $array;
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
