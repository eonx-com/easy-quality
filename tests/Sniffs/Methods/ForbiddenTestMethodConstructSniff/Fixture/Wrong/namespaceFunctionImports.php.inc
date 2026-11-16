<?php
declare(strict_types=1);

namespace App\Tests\BeforeImports;

final class BeforeImportsTest extends \PHPUnit\Framework\TestCase
{
    public function testBuiltin(): void
    {
        sleep(1);
    }
}

namespace App\Tests\CustomImports;

use function App\Clock\pause as sleep;

final class CustomImportsTest extends \PHPUnit\Framework\TestCase
{
    public function testCustom(): void
    {
        sleep();
    }
}

namespace App\Tests\ClassImports;

use App\Clock\Clock, App\Clock\Sleep;

final class ClassImportsTest extends \PHPUnit\Framework\TestCase
{
    public function testBuiltin(): void
    {
        sleep(1);
    }
}

namespace App\Tests\CustomImports;

final class RepeatedNamespaceTest extends \PHPUnit\Framework\TestCase
{
    public function testBuiltin(): void
    {
        sleep(1);
    }
}
