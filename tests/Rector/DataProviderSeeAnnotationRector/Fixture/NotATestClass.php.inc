<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\DataProviderSeeAnnotationRector\Fixture;

class NotATestClass
{
    public function provideSomeData(): array
    {
        return [
            'case 1' => ['first' => 2, 'second' => 2, 'result' => 4],
        ];
    }

    /**
     * @dataProvider provideSomeData
     */
    public function testAdd(int $first, int $second, int $result): void
    {
        $actualResult = $first + $second;

        self::assertSame($result, $actualResult);
    }
}
