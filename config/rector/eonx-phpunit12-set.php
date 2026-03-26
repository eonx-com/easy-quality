<?php
declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\PHPUnit120\Rector\CallLike\CreateStubInCoalesceArgRector;
use Rector\PHPUnit\PHPUnit120\Rector\CallLike\CreateStubOverCreateMockArgRector;
use Rector\PHPUnit\PHPUnit120\Rector\Class_\AssertIsTypeMethodCallRector;
use Rector\PHPUnit\PHPUnit120\Rector\Class_\PropertyCreateMockToCreateStubRector;
use Rector\PHPUnit\PHPUnit120\Rector\Class_\RemoveOverrideFinalConstructTestCaseRector;
use Rector\PHPUnit\PHPUnit120\Rector\ClassMethod\ExpressionCreateMockToCreateStubRector;

/**
 * @see https://github.com/rectorphp/rector-phpunit/blob/main/config/sets/phpunit120.php
 * @see https://phpunit.de/announcements/phpunit-12.html
 *
 * PHPUnit 12 builds on top of the PHPUnit 10 migration rules and adds
 * PHPUnit 12 specific transformations.
 */
return RectorConfig::configure()
    ->withSets([
        __DIR__ . '/eonx-phpunit10-set.php',
    ])
    ->withRules([
        RemoveOverrideFinalConstructTestCaseRector::class,
        AssertIsTypeMethodCallRector::class,
        CreateStubOverCreateMockArgRector::class,
        CreateStubInCoalesceArgRector::class,
        ExpressionCreateMockToCreateStubRector::class,
        PropertyCreateMockToCreateStubRector::class,
    ]);
