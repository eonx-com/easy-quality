<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Constants\DisallowBusinessConstantUsageInAssertBlock;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(DisallowBusinessConstantUsageInAssertBlock::class, [
        'testNamespace' => 'EonX\EasyQuality\Tests',
    ]);
};
