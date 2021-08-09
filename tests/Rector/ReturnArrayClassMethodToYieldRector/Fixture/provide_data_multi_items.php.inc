<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Fixture;

use PHPUnit\Framework\TestCase;

final class ProvideDataMultiItemsCase extends TestCase
{
    /**
     * @return mixed[]
     *
     * @see testSomething
     */
    public function provideData(): array
    {
        return [['7.5.0', false], ['7.5.0-13ubuntu3.2', false], ['7.1.0', true], ['7.1.0-13ubuntu3.2', true]];
    }
}

?>
-----
<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Fixture;

use PHPUnit\Framework\TestCase;

final class ProvideDataMultiItemsCase extends TestCase
{
    /**
     * @return mixed[]
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
}

?>
