<?php

declare(strict_types=1);

use EonX\EasyQuality\Rector\StrictInArrayRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(StrictInArrayRector::class);
};
