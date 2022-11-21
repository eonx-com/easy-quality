<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Classes\PropertyTypeSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(PropertyTypeSniff::class, [
        'replacePairs' => [
            'Carbon' => 'CarbonImmutable',
            'DateTime' => 'DateTimeImmutable',
            'integer' => 'string',
        ],
    ]);
};
