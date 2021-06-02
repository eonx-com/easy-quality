<?php
declare(strict_types=1);

use EonX\EasyQuality\Rector\AddCoversAnnotationRector;
use EonX\EasyQuality\Rector\AddSeeAnnotationRector;
use EonX\EasyQuality\Rector\ExplicitBoolCompareRector as EonxExplicitBoolCompareRector;
use EonX\EasyQuality\Rector\InheritDocRector;
use EonX\EasyQuality\Rector\PhpDocCommentRector;
use EonX\EasyQuality\Rector\RestoreDefaultNullToNullableTypeParameterRector;
use EonX\EasyQuality\Rector\SingleLineCommentRector;
use EonX\EasyQuality\Rector\StrictInArrayRector;
use EonX\EasyQuality\Rector\UselessSingleAnnotationRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->set(AddCoversAnnotationRector::class)
        ->set(AddSeeAnnotationRector::class)
        ->set(EonxExplicitBoolCompareRector::class)
        ->set(RestoreDefaultNullToNullableTypeParameterRector::class)
        ->set(StrictInArrayRector::class)
        ->set(InheritDocRector::class)
        ->set(UselessSingleAnnotationRector::class)
        ->call('configure', [
            [
                UselessSingleAnnotationRector::ANNOTATIONS => ['{@inheritDoc}'],
            ],
        ])
        ->set(PhpDocCommentRector::class)
        ->set(SingleLineCommentRector::class);
};
