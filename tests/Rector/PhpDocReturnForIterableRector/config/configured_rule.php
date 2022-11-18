<?php
declare(strict_types=1);

use EonX\EasyQuality\Rector\PhpDocReturnForIterableRector;
use EonX\EasyQuality\Rector\ValueObject\PhpDocReturnForIterable;
use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\EventSubscriberInterface;
use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;
use PHPUnit\Framework\TestCase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(PhpDocReturnForIterableRector::class, [
        PhpDocReturnForIterableRector::METHODS_TO_UPDATE => [
            new PhpDocReturnForIterable(EventSubscriberInterface::class, 'getSubscribedEvents'),
            new PhpDocReturnForIterable(ParentTestCase::class, 'provide*'),
            new PhpDocReturnForIterable(ParentTestCase::class, 'dataProvider*'),
            new PhpDocReturnForIterable(TestCase::class, 'provideData*'),
        ],
    ]);
};
