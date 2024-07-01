<?php
declare(strict_types=1);

use EonX\EasyQuality\Helper\ParallelSettingsHelper;

$config = [];
$config['parameters']['parallel'] = [
    'jobSize' => ParallelSettingsHelper::getJobSize(),
    'maximumNumberOfProcesses' => ParallelSettingsHelper::getMaxNumberOfProcess(),
    'processTimeout' => (float)ParallelSettingsHelper::getTimeoutSeconds(),
];

return $config;
