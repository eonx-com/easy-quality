<?php

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

final class MyTestCase2 extends ParentTestCase
{
    /**
     * @return mixed[]
     */
    public function provideData(): iterable
    {
        # some comment
        yield ['item'];
    }
}

?>
-----
<?php

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

final class MyTestCase2 extends ParentTestCase
{
    /**
     * @return iterable<mixed>
     */
    public function provideData(): iterable
    {
        # some comment
        yield ['item'];
    }
}

?>
