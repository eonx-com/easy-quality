<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

abstract class AbstractSecuredTestCase extends TestCase
{
    public function testInherited(): void
    {
        self::assertSame(1, \time() > 0 ? 1 : 0);
    }

    abstract public function testAbstract(): void;
}
