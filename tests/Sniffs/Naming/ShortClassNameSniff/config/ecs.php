<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Naming\ShortClassNameSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(ShortClassNameSniff::class, [
        'exceptions' => ['Id'],
    ]);
};
