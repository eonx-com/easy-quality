<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\AddSeeAnnotationRector\Fixture;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class MissingAttribute extends TestCase
{
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
}

?>
-----
<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\AddSeeAnnotationRector\Fixture;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class MissingAttribute extends TestCase
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
}

?>
