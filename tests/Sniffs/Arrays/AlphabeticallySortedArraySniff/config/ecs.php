<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Arrays\AlphabeticallySortedArraySniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(AlphabeticallySortedArraySniff::class, [
        'skipPatterns' => [
            T_CLASS => ['/^SomeClass/'],
            T_FUNCTION => ['/^provide/'],
        ],
    ]);
};
