<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Source\ParentTestCase;

final class MyTestCase3 extends ParentTestCase
{
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
     * @return iterable<mixed>
     */
    public function dataProvider(): iterable
    {
        yield [__DIR__ . '/Fixture/fixture.php.inc', __DIR__ . '/Correct/correct.php.inc'];
    }
}

?>
