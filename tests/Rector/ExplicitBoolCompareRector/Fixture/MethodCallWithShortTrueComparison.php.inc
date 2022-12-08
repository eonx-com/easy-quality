<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ExplicitBoolCompareRector\Fixture;

class MethodCallWithShortTrueComparison
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
