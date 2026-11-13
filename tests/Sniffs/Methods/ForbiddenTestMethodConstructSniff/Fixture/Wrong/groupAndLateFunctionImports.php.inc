<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use function App\Clock\{pause as sleep, value as rand, sleep as nap,};
use FUNCTION App\{Clock\wait as usleep};

final class GroupAndLateFunctionImportsTest extends \PHPUnit\Framework\TestCase
{
    public function testImports(): void
    {
        sleep();
        rand();
        usleep();
        nap();
        wait(1);
        shuffle([]);
    }
}

use function usleep as wait;
use function App\Fixtures\items as shuffle;
