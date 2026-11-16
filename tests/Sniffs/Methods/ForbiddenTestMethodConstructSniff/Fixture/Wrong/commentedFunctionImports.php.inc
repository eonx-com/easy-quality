<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use function App\Clock\{pause, // legacy, kept
    wait as sleep,};
use function \random_int /* seeded */ as number, App\Clock\shuffle;
use function usleep;

final class CommentedFunctionImportsTest extends \PHPUnit\Framework\TestCase
{
    public function testImports(): void
    {
        sleep();
        number(1, 2);
        pause();
        shuffle([]);
        usleep(1);
    }
}
