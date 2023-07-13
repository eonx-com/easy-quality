#!/usr/bin/env bash

# This script removes `/phpmetrics/phpmetrics/src/functions.php` file from autoload
# because it causes a fatal error when running rector tests.
#
# Removes this line `'5f0e95b8df5acf4a92c896dc3ac4bb6e' => __DIR__ . '/..' . '/phpmetrics/phpmetrics/src/functions.php',`
# from `./vendor/composer/autoload_static.php` file.

awk '!/5f0e95b8df5acf4a92c896dc3ac4bb6e/' ./vendor/composer/autoload_static.php > temp && mv temp ./vendor/composer/autoload_static.php
