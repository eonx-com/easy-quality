<?php

declare(strict_types=1);

use EonX\EasyQuality\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(UseYieldInsteadOfReturnSniff::class, [
        'applyTo' => [
            [
                'namespace' => '/^EonX\\\EasyQuality\\\Tests\\\Sniffs\\\ControlStructures\\\UseYieldInsteadOfReturnSniff\\\Fixtures\\\(Correct|Wrong)$/',
                'patterns' => ['/provide[A-Z]*/'],
            ],
        ]
    ]);
};
