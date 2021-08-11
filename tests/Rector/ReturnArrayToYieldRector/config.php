<?php

declare(strict_types=1);

use EonX\EasyQuality\Rector\ReturnArrayToYieldRector;
use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\EventSubscriberInterface;
use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\ParentTestCase;
use EonX\EasyQuality\ValueObject\ReturnArrayToYield;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Rector\SymfonyPhpConfig\inline_value_objects;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ReturnArrayToYieldRector::class)
        ->call('configure', [
            [
                ReturnArrayToYieldRector::METHODS_TO_YIELDS => inline_value_objects([
                    new ReturnArrayToYield(EventSubscriberInterface::class, 'getSubscribedEvents'),
                    new ReturnArrayToYield(ParentTestCase::class, 'provide*'),
                    new ReturnArrayToYield(ParentTestCase::class, 'dataProvider*'),
                    new ReturnArrayToYield(TestCase::class, 'provideData'),
                ]),
            ],
        ]);
};
