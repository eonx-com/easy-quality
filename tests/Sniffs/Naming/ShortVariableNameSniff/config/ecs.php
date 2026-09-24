<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Naming\ShortVariableNameSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(ShortVariableNameSniff::class, [
        'exceptions' => ['id'],
    ]);
};
