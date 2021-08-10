<?php

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;

final class ShouldNotApplyWhenMethodNameDiffers extends ParentTestCase
{
    public function getSomePublicData(): iterable
    {
        yield 'visibility' => 'public';
    }

    private function getSomePrivateData(): iterable
    {
        yield 'visibility' => 'private';
    }
}
