<?php
declare(strict_types=1);

namespace App\Unit {
    final class OutsideTestNamespaceTest
    {
        public function testValue(): int
        {
            return \time() > 0 ? 1 : 0;
        }
    }
}

namespace {
    final class WithoutNamespaceTest
    {
        public function testValue(): int
        {
            return \time() > 0 ? 1 : 0;
        }
    }
}
