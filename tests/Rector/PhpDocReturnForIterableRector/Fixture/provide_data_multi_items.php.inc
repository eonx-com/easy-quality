<?php

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use PHPUnit\Framework\TestCase;

final class ProvideDataMultiItemsCase extends TestCase
{
    /**
     * @return iterable<string, bool>
     *
     * @see testSomething
     */
    public function provideData(): iterable
    {
        yield ['7.5.0', false];
        yield ['7.5.0-13ubuntu3.2', false];
        yield ['7.1.0', true];
        yield ['7.1.0-13ubuntu3.2', true];
    }

    /**
     * @return array[]
     */
    public function provideData2(): iterable
    {
        yield [1];
    }
}

?>
-----
<?php

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use PHPUnit\Framework\TestCase;

final class ProvideDataMultiItemsCase extends TestCase
{
    /**
     * @return iterable<string, bool>
     *
     * @see testSomething
     */
    public function provideData(): iterable
    {
        yield ['7.5.0', false];
        yield ['7.5.0-13ubuntu3.2', false];
        yield ['7.1.0', true];
        yield ['7.1.0-13ubuntu3.2', true];
    }

    /**
     * @return iterable<mixed>
     */
    public function provideData2(): iterable
    {
        yield [1];
    }
}

?>
