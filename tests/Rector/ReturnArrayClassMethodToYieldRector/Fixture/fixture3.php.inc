<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Source\ParentTestCase;

final class MyTestCase3 extends ParentTestCase
{
    /**
     * @todo Some sing.
     * @cutomTag Tag value.
     *
     * Custom Text
     *
     * @return iterable<string>
     *
     * Another Custom Text
     */
    public function dataProvider(): array
    {
        return [[__DIR__ . '/Fixture/fixture.php.inc', __DIR__ . '/Correct/correct.php.inc']];
    }
}

?>
-----
<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Source\ParentTestCase;

final class MyTestCase3 extends ParentTestCase
{
    /**
     * @todo Some sing.
     * @cutomTag Tag value.
     *
     * Custom Text
     *
     * @return iterable<string>
     *
     * Another Custom Text
     */
    public function dataProvider(): iterable
    {
        yield [__DIR__ . '/Fixture/fixture.php.inc', __DIR__ . '/Correct/correct.php.inc'];
    }
}

?>
