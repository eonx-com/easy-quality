<?php

declare(strict_types=1);

use EonX\EasyQuality\Sniffs\ControlStructures\ArrangeActAssertSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(ArrangeActAssertSniff::class);
};
