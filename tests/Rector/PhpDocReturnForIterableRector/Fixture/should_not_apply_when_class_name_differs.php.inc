<?php

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

final class ShouldNotApplyWhenClassNameDiffers
{
    /**
     * @return mixed[]
     */
    public function getSubscribedEvents(): iterable
    {
        yield 'some' => 'event';
    }

    public function provideSomeData(): iterable
    {
        yield 'some' => 'data';
    }
}
