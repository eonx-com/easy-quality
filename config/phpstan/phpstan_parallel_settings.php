<?php
declare(strict_types=1);

use EonX\EasyQuality\Helper\ParallelSettingsResolver;

$config = [];
$config['parameters']['parallel'] = [
    'jobSize' => ParallelSettingsResolver::resolveJobSize(),
    'maximumNumberOfProcesses' => ParallelSettingsResolver::resolveMaxNumberOfProcess(),
    'processTimeout' => (float)ParallelSettingsResolver::resolveTimeoutSeconds(),
];

return $config;
