<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Naming\ShortVariableNameSniff\Fixture\Wrong;

final class Example
{
    private int $x;

    public function __construct(private readonly string $y)
    {
    }

    public function run(): int
    {
        $a = 1;
        $a = $a + 1;

        return $a;
    }

    public function runAgain(): int
    {
        $a = 2;

        return $a;
    }

    public function withParam(int $a): int
    {
        return $a;
    }

    public function withParamAgain(int $a): int
    {
        return $a;
    }
}
