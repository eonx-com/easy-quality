<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Naming\ShortMethodNameSniff\Fixture\Correct;

final class Converter
{
    public function convert(): void
    {
    }

    public function to(): void
    {
    }

    public function anonymous(): void
    {
        $callback = function (): void {
        };
        $arrow = fn (): int => 1;
    }
}
