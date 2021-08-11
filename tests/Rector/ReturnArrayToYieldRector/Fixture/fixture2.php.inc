<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;

final class MyTestCase2 extends ParentTestCase
{
    /**
     * @return mixed[]
     */
    public function provideData(): array
    {
        return [
            # some comment
            ['item']
        ];
    }
}

?>
-----
<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;

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
