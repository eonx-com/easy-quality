<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Classes\AvoidPublicPropertiesSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(AvoidPublicPropertiesSniff::class);
};
