<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Naming\ShortVariableNameSniff\Fixture\Wrong;

final class FixerLoopStability
{
    public function run(): int
    {
        $a = 1;

        return $a;
    }
}-----
<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Naming\ShortVariableNameSniff\Fixture\Wrong;

final class FixerLoopStability
{
    public function run(): int
    {
        $a = 1;

        return $a;
    }
}
