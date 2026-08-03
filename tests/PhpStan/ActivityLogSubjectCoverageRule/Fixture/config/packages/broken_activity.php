<?php
declare(strict_types=1);

// Stands in for a config file calling a helper that is neither autoloaded nor declared in a reference file
return MissingConfigHelper::config([
    'easy_activity' => [
        'subjects' => [],
    ],
]);
