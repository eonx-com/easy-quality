<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Attributes\DoctrineColumnTypeSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(DoctrineColumnTypeSniff::class, [
        'replacePairs' => [
            'date' => 'date_immutable',
            'datetime' => 'datetime_immutable',
            'string' => 'Types::STRING',
            'Types::DATE_MUTABLE' => 'Types::DATE_IMMUTABLE',
            'Types::DATETIME_MUTABLE' => 'Types::DATETIME_IMMUTABLE',
        ],
    ]);
};
