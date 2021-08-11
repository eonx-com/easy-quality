<?php

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

final class MyTestCase3 extends ParentTestCase
{
    /**
     * @todo Some sing.
     * @cutomTag Tag value.
     *
     * Custom Text
     *
     * @return iterable
     *
     * Another Custom Text
     */
    public function dataProvider(): iterable
    {
        yield [__DIR__ . '/Fixture/fixture.php.inc', __DIR__ . '/Correct/correct.php.inc'];
    }
}

?>
-----
<?php

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

final class MyTestCase3 extends ParentTestCase
{
    /**
     * @todo Some sing.
     * @cutomTag Tag value.
     *
     * Custom Text
     *
     * @return iterable<mixed>
     *
     * Another Custom Text
     */
    public function dataProvider(): iterable
    {
        yield [__DIR__ . '/Fixture/fixture.php.inc', __DIR__ . '/Correct/correct.php.inc'];
    }
}

?>
