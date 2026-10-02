<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Naming\ShortMethodNameSniff\Fixture\Wrong;

final class Converter
{
    public function go(): void
    {
    }
}

function at(): void
{
}
