<?php

declare(strict_types=1);

use EonX\EasyQuality\Rector\AddCoversAnnotationRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(AddCoversAnnotationRector::class, [
        AddCoversAnnotationRector::REPLACE_ARRAY => ['Tests\\Unit\\'],
    ]);
};
