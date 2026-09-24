<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Naming\ShortVariableNameSniff\Fixture\Correct;

final class Example
{
    public function run(array $items): void
    {
        $id = 1;

        try {
            $this->doSomething();
        } catch (\Throwable $e) {
        }

        for ($i = 0; $i < 10; $i++) {
        }

        foreach ($items as $k => $v) {
            echo $k, $v;
        }
    }

    private function doSomething(): void
    {
    }
}
