<?php
declare(strict_types=1);

use EonX\EasyQuality\Rector\CompletePartialNamedArgumentsStrategy;
use Rector\Config\RectorConfig;
use SavinMikhail\AddNamedArgumentsRector\AddNamedArgumentsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(AddNamedArgumentsRector::class, [
        AddNamedArgumentsRector::STRATEGY => CompletePartialNamedArgumentsStrategy::class,
    ]);
};
