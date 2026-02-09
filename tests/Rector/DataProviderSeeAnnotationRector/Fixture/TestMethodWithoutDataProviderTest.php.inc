<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\DataProviderSeeAnnotationRector\Fixture;

use PHPUnit\Framework\TestCase;

class TestMethodWithoutDataProviderTest extends TestCase
{
    public function testAdd(): void
    {
        self::assertTrue(true);
    }
}
