<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Source\ParentTestCase;

class DataProvider2 extends ParentTestCase
{
    public static function provideData1()
    {
        $inputData = [1,2,3];
        $array = [];
        foreach ($inputData as $data){
            $array[] = $data;
        }

        //some comment
        return $array;
    }

    /**
     * @return array[]
     *
     * @see testSomething
     */
    public static function provideData2()
    {
        $inputData = [1,2,3];
        $array = [];
        foreach ($inputData as $key => $data){
            $array[$key] = $data;
        }

        return $array;
    }

    public static function provideData3()
    {
        $inputData = [1,2,3];
        $array = [];
        foreach ($inputData as $key => $data){
            $array['Some key '.$key] = ['data' => $data];
        }

        return $array;
    }
}

?>
-----
<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Source\ParentTestCase;

class DataProvider2 extends ParentTestCase
{
    /**
     * @return iterable<mixed>
     *
     * some comment
     */
    public static function provideData1(): iterable
    {
        $inputData = [1,2,3];
        $array = [];
        foreach ($inputData as $data){
            $array[] = $data;
        }

        yield $array;
    }

    /**
     * @return iterable<mixed>
     *
     * @see testSomething
     */
    public static function provideData2(): iterable
    {
        $inputData = [1,2,3];
        $array = [];
        foreach ($inputData as $key => $data){
            $array[$key] = $data;
        }

        yield $array;
    }

    /**
     * @return iterable<mixed>
     */
    public static function provideData3(): iterable
    {
        $inputData = [1,2,3];
        $array = [];
        foreach ($inputData as $key => $data){
            $array['Some key '.$key] = ['data' => $data];
        }

        yield $array;
    }
}

?>
