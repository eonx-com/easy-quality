<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;

final class MyTestCase extends ParentTestCase
{
    /**
     * @return mixed[]
     *
     * @see testSomething
     */
    public function provideData(): array
    {
        return [
            ['item']
        ];
    }
}

?>
-----
<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;

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
