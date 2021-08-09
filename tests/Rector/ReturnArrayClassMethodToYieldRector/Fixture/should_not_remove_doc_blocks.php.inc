<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Source\ParentTestCase;

final class ShouldNotRemoveComment extends ParentTestCase
{
    public function provideData(): array
    {
        /**
         * @doto Let's pretend that this comment is extremely important and meaningful.
         *
         * It should not be removed by Rector.
         */
        return [
            /**
             * @doto Yet another important comment
             */
            ['item1'],
            /**
             * @doto One final important comment
             */
            ['item2'],
        ];
    }
}

?>
-----
<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Source\ParentTestCase;

final class ShouldNotRemoveComment extends ParentTestCase
{
    /**
     * @return mixed[]
     *
     * @doto Let's pretend that this comment is extremely important and meaningful.
     *
     * It should not be removed by Rector.
     */
    public function provideData(): iterable
    {
        /**
         * @doto Yet another important comment
         */
        yield ['item1'];
        /**
         * @doto One final important comment
         */
        yield ['item2'];
    }
}

?>
