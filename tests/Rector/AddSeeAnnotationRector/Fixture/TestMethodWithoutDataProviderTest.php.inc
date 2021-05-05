<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\AddSeeAnnotationRector\Fixture;

use PHPUnit\Framework\TestCase;

class TestMethodWithoutDataProviderTest extends TestCase
{
    /**
     * Test add.
     *
     * @return void
     */
    public function testAdd(): void
    {
        self::assertTrue(true);
    }
}
