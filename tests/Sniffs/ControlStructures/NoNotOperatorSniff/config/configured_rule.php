<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\ControlStructures\NoNotOperatorSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(NoNotOperatorSniff::class);
};
