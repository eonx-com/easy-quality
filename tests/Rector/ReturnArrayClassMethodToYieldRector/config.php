<?php

declare(strict_types=1);

use EonX\EasyQuality\Rector\ReturnArrayClassMethodToYieldRector;
use EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Source\EventSubscriberInterface;
use EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Source\ParentTestCase;
use PHPUnit\Framework\TestCase;
use Rector\CodingStyle\ValueObject\ReturnArrayClassMethodToYield;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Rector\SymfonyPhpConfig\inline_value_objects;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ReturnArrayClassMethodToYieldRector::class)
        ->call('configure', [
            [
                ReturnArrayClassMethodToYieldRector::METHODS_TO_YIELDS => inline_value_objects([
                    new ReturnArrayClassMethodToYield(EventSubscriberInterface::class, 'getSubscribedEvents'),
                    new ReturnArrayClassMethodToYield(ParentTestCase::class, 'provide*'),
                    new ReturnArrayClassMethodToYield(ParentTestCase::class, 'dataProvider*'),
                    new ReturnArrayClassMethodToYield(TestCase::class, 'provideData'),
                ]),
            ],
        ]);
};
