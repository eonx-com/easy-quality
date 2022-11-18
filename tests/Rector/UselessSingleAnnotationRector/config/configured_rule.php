<?php
declare(strict_types=1);

use EonX\EasyQuality\Rector\UselessSingleAnnotationRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(UselessSingleAnnotationRector::class, [
        UselessSingleAnnotationRector::ANNOTATIONS => ['{@inheritDoc}'],
    ]);
};
