<?php

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

final class MyTestCase3 extends ParentTestCase
{
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
     * @return iterable<mixed>
     */
    public function dataProvider(): iterable
    {
        yield [__DIR__ . '/Fixture/fixture.php.inc', __DIR__ . '/Correct/correct.php.inc'];
    }
}

?>
