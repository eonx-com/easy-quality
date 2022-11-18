<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ExplicitBoolCompareRector\Fixture;

class MethodCallWithExplicitTrueComparison
{
    public function testProcess(): void
    {
        if ($this->isValid() === true) {
            echo 'valid';
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

class MethodCallWithExplicitTrueComparison
{
    public function testProcess(): void
    {
        if ($this->isValid()) {
            echo 'valid';
        }
    }

    public function isValid(): bool
    {
        return true;
    }
}

?>
