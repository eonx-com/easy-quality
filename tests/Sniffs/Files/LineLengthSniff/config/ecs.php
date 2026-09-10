<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Files\LineLengthSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(LineLengthSniff::class, [
        'absoluteLineLimit' => 100,
        'ignoreConstants' => true,
        'ignoreEnums' => true,
        'ignoreStaticMethods' => true,
    ]);
};
