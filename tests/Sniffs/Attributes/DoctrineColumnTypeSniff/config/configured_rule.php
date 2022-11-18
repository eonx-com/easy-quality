<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Attributes\DoctrineColumnTypeSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(DoctrineColumnTypeSniff::class, [
        'replacePairs' => [
            'date' => 'date_immutable',
            'datetime' => 'datetime_immutable',
            'Types::DATE' => 'Types::DATE_IMMUTABLE',
            'string' => 'Types::STRING',
        ]
    ]);
};
