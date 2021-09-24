<?php

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

final class MyTestCase3 extends ParentTestCase
{
    /**
     * Custom Text
     *
     * @todo Some sing.
     * @cutomTag Tag value.
     *
     * @return iterable
     *
     * Another Custom Text that appear in the bottom
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
     * Custom Text
     *
     * @todo Some sing.
     * @cutomTag Tag value.
     *
     * @return iterable<mixed>
     *
     * Another Custom Text that appear in the bottom
     */
    public function dataProvider(): iterable
    {
        yield [__DIR__ . '/Fixture/fixture.php.inc', __DIR__ . '/Correct/correct.php.inc'];
    }
}

?>
