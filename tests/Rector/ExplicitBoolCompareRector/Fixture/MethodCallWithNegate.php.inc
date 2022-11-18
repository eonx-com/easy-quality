<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ExplicitBoolCompareRector\Fixture;

class MethodCallWithNegate
{
    public function testProcess(): void
    {
        if (!$this->isValid()) {
            echo 'not valid';
        }
    }

    public function isValid(): bool
    {
        return true;
    }
}

?>
    -----
    <?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ExplicitBoolCompareRector\Fixture;

class MethodCallWithNegate
{
    public function testProcess(): void
    {
        if ($this->isValid() === false) {
            echo 'not valid';
        }
    }

    public function isValid(): bool
    {
        return true;
    }
}

?>
