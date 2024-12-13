<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Attributes\SortedApiResourceOperationsSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(SortedApiResourceOperationsSniff::class);
};
