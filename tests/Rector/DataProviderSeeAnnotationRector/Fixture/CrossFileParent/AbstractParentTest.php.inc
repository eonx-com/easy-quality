<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\DataProviderSeeAnnotationRector\Fixture\CrossFileParent;

use PHPUnit\Framework\TestCase;

abstract class AbstractParentTest extends TestCase
{
    public function provideAddData(): array
    {
        return [
            'case 1' => ['first' => 2, 'second' => 2, 'result' => 4],
        ];
    }
}

?>
-----
<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\DataProviderSeeAnnotationRector\Fixture\CrossFileParent;

use PHPUnit\Framework\TestCase;

abstract class AbstractParentTest extends TestCase
{
    /**
     * @see ChildTest::testAdd
     */
    public function provideAddData(): array
    {
        return [
            'case 1' => ['first' => 2, 'second' => 2, 'result' => 4],
        ];
    }
}

?>
