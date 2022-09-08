<?php

declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Classes\AvoidPrivatePropertiesSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(AvoidPrivatePropertiesSniff::class, [
        'applyTo' => [
            '/^App\\\.*(ApiResource|Entity)/',
        ],
    ]);
};
