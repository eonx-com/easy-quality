<?php

declare(strict_types=1);

use EonX\EasyQuality\Rector\PhpDocReturnForIterableRector;
use EonX\EasyQuality\Rector\ValueObject\PhpDocReturnForIterable;
use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\EventSubscriberInterface;
use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\ParentTestCase;
use PHPUnit\Framework\TestCase;
use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->parameters()
        ->set(Option::PATHS, [
            __DIR__ . '/../Source',
            __DIR__ . '/../Fixture',
        ])
        ->set(Option::AUTO_IMPORT_NAMES, true)
        ->set(Option::IMPORT_SHORT_CLASSES, true)
        ->set(Option::IMPORT_DOC_BLOCKS, false)
        ->set(Option::AUTOLOAD_PATHS, [__DIR__ . '/../../../../vendor']);


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
