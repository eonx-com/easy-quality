<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Fixture;

final class ShouldNotApplyWhenClassNameDiffers
{
    public function getSubscribedEvents(): array
    {
        return ['some' => 'event'];
    }

    public function provideSomeData(): array
    {
        return ['some' => 'data'];
    }
}
