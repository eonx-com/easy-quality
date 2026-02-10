<?php
declare(strict_types=1);

use EonX\EasyQuality\Rector\DataProviderSeeAnnotationRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(DataProviderSeeAnnotationRector::class);
};
