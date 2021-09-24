<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;

final class MyTestCase3 extends ParentTestCase
{
    /**
     * Custom Text
     *
     * Another Custom Text
     *
     * @todo Some thing.
     * @customTag Tag value.
     *
     * @return iterable<string>
     */
    public function dataProvider(): array
    {
        return [[__DIR__ . '/Fixture/fixture.php.inc', __DIR__ . '/Correct/correct.php.inc']];
    }
}

?>
-----
<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;

final class MyTestCase3 extends ParentTestCase
{
    /**
     * Custom Text
     *
     * Another Custom Text
     *
     * @todo Some thing.
     * @customTag Tag value.
     *
     * @return iterable<string>
     */
    public function dataProvider(): iterable
    {
        yield [__DIR__ . '/Fixture/fixture.php.inc', __DIR__ . '/Correct/correct.php.inc'];
    }
}

?>
