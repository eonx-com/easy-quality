<?php

declare(strict_types=1);

use EonX\EasyQuality\Rector\EasyRankeable\ApiResourceOperationEasyRankeable;
use EonX\EasyQuality\Rector\SortApiResourceOperationsRector;
use EonX\EasyQuality\Rector\ValueObject\SortApiResourceOperations;
use RectorPrefix20220126\Symplify\SymfonyPhpConfig\ValueObjectInliner;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(SortApiResourceOperationsRector::class)->call('configure', [
        [
            SortApiResourceOperationsRector::API_RESOURCE_FQCN => 'ApiResource',
            SortApiResourceOperationsRector::RANKEABLE_CLASS => ValueObjectInliner::inline(
                new SortApiResourceOperations(ApiResourceOperationEasyRankeable::class)
            ),
        ],
    ]);
};
