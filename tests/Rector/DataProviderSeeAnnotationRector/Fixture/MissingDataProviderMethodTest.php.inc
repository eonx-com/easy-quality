<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\DataProviderSeeAnnotationRector\Fixture;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MissingDataProviderMethodTest extends TestCase
{
    #[DataProvider('provideSomeData')]
    public function testAdd(int $first, int $second, int $result): void
    {
        $actualResult = $first + $second;

        self::assertSame($result, $actualResult);
    }
}
