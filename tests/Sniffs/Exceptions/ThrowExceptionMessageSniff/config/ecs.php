<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Exceptions\ThrowExceptionMessageSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(ThrowExceptionMessageSniff::class, [
        'validPrefixes' => ['exceptions'],
    ]);
};
