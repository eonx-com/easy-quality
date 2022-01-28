<?php

declare(strict_types=1);

use EonX\EasyQuality\Rector\PhpDocReturnForIterableRector;
use EonX\EasyQuality\Rector\ValueObject\PhpDocReturnForIterable;
use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\EventSubscriberInterface;
use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;
use PHPUnit\Framework\TestCase;
use RectorPrefix20220126\Symplify\SymfonyPhpConfig\ValueObjectInliner;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

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
