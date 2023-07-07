<?php
declare(strict_types=1);

/* Trying to fix the rector tests issue. It works but on second run.
$contents = \file_get_contents(__DIR__ . '/../vendor/composer/autoload_static.php');
$contents = \str_replace(
    "'5f0e95b8df5acf4a92c896dc3ac4bb6e' => __DIR__ . '/..' . '/phpmetrics/phpmetrics/src/functions.php',",
    '',
    $contents
);
\file_put_contents(__DIR__ . '/../vendor/composer/autoload_static.php', $contents);
*/

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/symplify/easy-coding-standard/vendor/squizlabs/php_codesniffer/autoload.php';
// @todo remove after resolving https://github.com/symplify/symplify/issues/4461
require_once __DIR__ . '/../vendor/symplify/easy-coding-standard/vendor/symfony/deprecation-contracts/function.php';
