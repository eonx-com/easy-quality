<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;

final class ShouldNotRemoveComments extends ParentTestCase
{
    public function provideData(): array
    {
        // Let's pretend that this comment is extremely important and meaningful.
        // It should not be removed by Rector.
        return [
            // This is yet another comment.
            ['item1'],

            // And a final one.
            ['item2'],
        ];
    }
}

?>
-----
<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;

final class ShouldNotRemoveComments extends ParentTestCase
{
    /**
     * Let's pretend that this comment is extremely important and meaningful.
     * It should not be removed by Rector.
     *
     * @return iterable<mixed>
     */
    public function provideData(): iterable
    {
        // This is yet another comment.
        yield ['item1'];
        // And a final one.
        yield ['item2'];
    }
}

?>
