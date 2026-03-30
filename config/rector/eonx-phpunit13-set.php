<?php
declare(strict_types=1);

use Rector\Config\RectorConfig;

/**
 * Rector does not provide a dedicated PHPUnit 13 set yet, so we currently
 * reuse the PHPUnit 12 rules as the closest upstream-maintained migration set.
 *
 * @see https://github.com/rectorphp/rector-phpunit/blob/main/src/Set/PHPUnitSetList.php
 */
return RectorConfig::configure()
    ->withSets([
        __DIR__ . '/eonx-phpunit12-set.php',
    ]);
