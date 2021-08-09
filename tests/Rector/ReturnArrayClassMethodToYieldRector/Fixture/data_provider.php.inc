<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Source\ParentTestCase;

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

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Source\ParentTestCase;

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
     * @return mixed[]
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
