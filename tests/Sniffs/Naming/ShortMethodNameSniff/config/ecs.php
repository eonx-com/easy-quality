<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Naming\ShortMethodNameSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(ShortMethodNameSniff::class, [
        'exceptions' => ['to'],
    ]);
};
