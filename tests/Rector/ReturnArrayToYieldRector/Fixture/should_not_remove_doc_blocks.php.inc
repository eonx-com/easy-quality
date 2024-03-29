<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;

final class ShouldNotRemoveComment extends ParentTestCase
{
    public function provideData(): array
    {
        /**
         * It should not be removed by Rector.
         *
         * @todo Let's pretend that this comment is extremely important and meaningful.
         */
        return [
            /**
             * @todo Yet another important comment
             */
            ['item1'],
            /**
             * @todo One final important comment
             */
            ['item2'],
        ];
    }
}

?>
-----
<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;

final class ShouldNotRemoveComment extends ParentTestCase
{
    /**
     * It should not be removed by Rector.
     *
     * @todo Let's pretend that this comment is extremely important and meaningful.
     *
     * @return iterable<mixed>
     */
    public function provideData(): iterable
    {
        /**
         * @todo Yet another important comment
         */
        yield ['item1'];
        /**
         * @todo One final important comment
         */
        yield ['item2'];
    }
}

?>
