<?php

declare(strict_types=1);

use EonX\EasyQuality\Rector\InheritDocRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(InheritDocRector::class);
};
