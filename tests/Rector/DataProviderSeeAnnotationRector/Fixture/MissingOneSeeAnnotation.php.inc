<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\DataProviderSeeAnnotationRector\Fixture;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MissingOneSeeAnnotation extends TestCase
{
    /**
     * @see testAdd
     */
    public function provideData(): array
    {
        return [
            'case 1' => ['first' => 2, 'second' => 2, 'result' => 4],
        ];
    }

    #[DataProvider('provideData')]
    public function testAdd(int $first, int $second, int $result): void
    {
        self::assertSame($result, $first + $second);
    }

    #[DataProvider('provideData')]
    public function testMultiply(int $first, int $second, int $result): void
    {
        self::assertIsInt($first * $second);
    }
}

?>
-----
<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\DataProviderSeeAnnotationRector\Fixture;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MissingOneSeeAnnotation extends TestCase
{
    /**
     * @see testAdd
     * @see testMultiply
     */
    public function provideData(): array
    {
        return [
            'case 1' => ['first' => 2, 'second' => 2, 'result' => 4],
        ];
    }

    #[DataProvider('provideData')]
    public function testAdd(int $first, int $second, int $result): void
    {
        self::assertSame($result, $first + $second);
    }

    #[DataProvider('provideData')]
    public function testMultiply(int $first, int $second, int $result): void
    {
        self::assertIsInt($first * $second);
    }
}

?>
