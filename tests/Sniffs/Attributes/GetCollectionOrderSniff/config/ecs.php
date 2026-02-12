<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Attributes\GetCollectionOrderSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(GetCollectionOrderSniff::class);
};
