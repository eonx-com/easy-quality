<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\DataProviderSeeAnnotationRector\Fixture\CrossFileParent;

use PHPUnit\Framework\Attributes\DataProvider;

final class ChildTest extends AbstractParentTest
{
    #[DataProvider('provideAddData')]
    public function testAdd(int $first, int $second, int $result): void
    {
        self::assertSame($result, $first + $second);
    }
}
