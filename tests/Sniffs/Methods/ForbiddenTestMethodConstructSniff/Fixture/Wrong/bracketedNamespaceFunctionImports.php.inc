<?php
declare(strict_types=1);

namespace App\Tests\CustomImports {
    use function App\Clock\pause as sleep;

    final class CustomImportsTest extends \PHPUnit\Framework\TestCase
    {
        public function testCustom(): void
        {
            sleep();
        }
    }
}

namespace App\Tests\WithoutImports {
    final class WithoutImportsTest extends \PHPUnit\Framework\TestCase
    {
        public function testBuiltin(): void
        {
            sleep(1);
        }
    }
}
