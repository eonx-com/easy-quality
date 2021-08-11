<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;

final class ShouldNotApplyWhenMethodNameDiffers extends ParentTestCase
{
    public function getSomePublicData(): array
    {
        return ['visibility' => 'public'];
    }

    private function getSomePrivateData(): array
    {
        return ['visibility' => 'private'];
    }
}
