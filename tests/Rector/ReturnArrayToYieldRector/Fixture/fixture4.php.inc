<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;

final class MyTestCase extends ParentTestCase
{
    /**
     * Some structure sample
     * [
     *     'a' => 1,
     *     'b' => 2
     * ]
     *
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
     * Some structure sample
     * [
     *     'a' => 1,
     *     'b' => 2
     * ]
     *
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
