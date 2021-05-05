<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ExplicitBoolCompareRector\Fixture;

class MethodCallWithNegate
{
    public function isValid(): bool
    {
        return true;
    }

    public function testProcess(): void
    {
        if (!$this->isValid()) {
            $result = 'not valid';
        }
    }
}

?>
-----
<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ExplicitBoolCompareRector\Fixture;

class MethodCallWithNegate
{
    public function isValid(): bool
    {
        return true;
    }

    public function testProcess(): void
    {
        if ($this->isValid() === false) {
            $result = 'not valid';
        }
    }
}

?>
