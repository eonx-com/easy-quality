<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Constants\DisallowApplicationConstantAndEnumUsageInTestAssertBlock;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(DisallowApplicationConstantAndEnumUsageInTestAssertBlock::class, [
        'testNamespace' => 'EonX\EasyQuality\Tests',
    ]);
};
