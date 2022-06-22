<?php

declare(strict_types=1);

use EonX\EasyQuality\Rector\ReturnArrayToYieldRector;
use EonX\EasyQuality\Rector\ValueObject\ReturnArrayToYield;
use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\EventSubscriberInterface;
use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;
use PHPUnit\Framework\TestCase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(ReturnArrayToYieldRector::class, [
        ReturnArrayToYieldRector::METHODS_TO_YIELDS => [
            new ReturnArrayToYield(EventSubscriberInterface::class, 'getSubscribedEvents'),
            new ReturnArrayToYield(ParentTestCase::class, 'provide*'),
            new ReturnArrayToYield(ParentTestCase::class, 'dataProvider*'),
            new ReturnArrayToYield(TestCase::class, 'provideData'),
        ],
    ]);
};
