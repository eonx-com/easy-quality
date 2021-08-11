<?php

declare(strict_types=1);

use EonX\EasyQuality\Rector\PhpDocReturnForIterableRector;
use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\EventSubscriberInterface;
use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;
use EonX\EasyQuality\ValueObject\PhpDocReturnForIterable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(PhpDocReturnForIterableRector::class)
        ->call('configure', [
            [
                PhpDocReturnForIterableRector::METHODS_TO_UPDATE => ValueObjectInliner::inline([
                    new PhpDocReturnForIterable(EventSubscriberInterface::class, 'getSubscribedEvents'),
                    new PhpDocReturnForIterable(ParentTestCase::class, 'provide*'),
                    new PhpDocReturnForIterable(ParentTestCase::class, 'dataProvider*'),
                    new PhpDocReturnForIterable(TestCase::class, 'provideData*'),
                ]),
            ],
        ]);
};
