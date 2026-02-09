<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\DataProviderSeeAnnotationRector\Fixture;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RemoveRedundantSeeAnnotations extends TestCase
{
    /**
     * @see testAdd
     * @see testSubtract
     * @see testMultiply
     */
    public function provideSomeData(): array
    {
        return [
            'case 1' => ['first' => 2, 'second' => 2, 'result' => 4],
            'case 2' => ['first' => 3, 'second' => 5, 'result' => 8],
        ];
    }

    #[DataProvider('provideSomeData')]
    public function testAdd(int $first, int $second, int $result): void
    {
        $actualResult = $first + $second;

        self::assertSame($result, $actualResult);
    }

    public function testMultiply(): void
    {
        self::assertSame(6, 2 * 3);
    }
}

?>
-----
<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\DataProviderSeeAnnotationRector\Fixture;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RemoveRedundantSeeAnnotations extends TestCase
{
    /**
     * @see testAdd
     */
    public function provideSomeData(): array
    {
        return [
            'case 1' => ['first' => 2, 'second' => 2, 'result' => 4],
            'case 2' => ['first' => 3, 'second' => 5, 'result' => 8],
        ];
    }

    #[DataProvider('provideSomeData')]
    public function testAdd(int $first, int $second, int $result): void
    {
        $actualResult = $first + $second;

        self::assertSame($result, $actualResult);
    }

    public function testMultiply(): void
    {
        self::assertSame(6, 2 * 3);
    }
}

?>