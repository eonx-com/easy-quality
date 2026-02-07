<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\AddSeeAnnotationRector\Fixture;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UpdateAndRemoveSeeAnnotations extends TestCase
{
    /**
     * @see testOldMethod
     * @see testSubtract
     */
    public function provideAddData(): array
    {
        return [
            'case 1' => ['first' => 2, 'second' => 2, 'result' => 4],
        ];
    }

    /**
     * @see testSubtract
     * @see testAnotherOldMethod
     */
    public function provideSubtractData(): array
    {
        return [
            'case 1' => ['first' => 5, 'second' => 2, 'result' => 3],
        ];
    }

    #[DataProvider('provideAddData')]
    public function testAdd(int $first, int $second, int $result): void
    {
        self::assertSame($result, $first + $second);
    }

    #[DataProvider('provideSubtractData')]
    public function testSubtract(int $first, int $second, int $result): void
    {
        self::assertSame($result, $first - $second);
    }

    #[DataProvider('provideAddData')]
    public function testMultiply(int $first, int $second): void
    {
        self::assertIsInt($first * $second);
    }
}

?>
-----
<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\AddSeeAnnotationRector\Fixture;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UpdateAndRemoveSeeAnnotations extends TestCase
{
    /**
     * @see testAdd
     * @see testMultiply
     */
    public function provideAddData(): array
    {
        return [
            'case 1' => ['first' => 2, 'second' => 2, 'result' => 4],
        ];
    }

    /**
     * @see testSubtract
     */
    public function provideSubtractData(): array
    {
        return [
            'case 1' => ['first' => 5, 'second' => 2, 'result' => 3],
        ];
    }

    #[DataProvider('provideAddData')]
    public function testAdd(int $first, int $second, int $result): void
    {
        self::assertSame($result, $first + $second);
    }

    #[DataProvider('provideSubtractData')]
    public function testSubtract(int $first, int $second, int $result): void
    {
        self::assertSame($result, $first - $second);
    }

    #[DataProvider('provideAddData')]
    public function testMultiply(int $first, int $second): void
    {
        self::assertIsInt($first * $second);
    }
}

?>