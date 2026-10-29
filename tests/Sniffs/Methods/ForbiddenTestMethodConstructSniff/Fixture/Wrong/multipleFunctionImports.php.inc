<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use function sleep as pause, \random_int as number, usleep as microPause;
use function App\Clock\pause as sleep, App\Fixtures\value as rand;

final class MultipleFunctionImportsTest extends TestCase
{
    public function testImports(): void
    {
        pause(1);
        NUMBER(1, 10);
        microPause(1);
        sleep();
        rand();
        \sleep(1);
    }
}
