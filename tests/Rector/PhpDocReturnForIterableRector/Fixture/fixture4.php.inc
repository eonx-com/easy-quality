<?php

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

final class MyTestCase extends ParentTestCase
{
    /**
     * @return mixed[]
     *
     * @see testSomething
     */
    public function provideData(): iterable
    {
        yield ['item'];
    }
}

?>
-----
<?php

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

final class MyTestCase extends ParentTestCase
{
    /**
     * @return iterable<mixed>
     *
     * @see testSomething
     */
    public function provideData(): iterable
    {
        yield ['item'];
    }
}

?>
