<?php
declare(strict_types=1);

use EonX\EasyQuality\Rector\EasyRankeable\ApiResourceOperationEasyRankeable;
use EonX\EasyQuality\Rector\EasyRankeable\ClassConstantEasyRankeable;
use EonX\EasyQuality\Rector\EasyRankeable\ClassMethodEasyRankeable;
use EonX\EasyQuality\Rector\EasyRankeable\PropertyEasyRankeable;
use EonX\EasyQuality\Rector\ExplicitBoolCompareRector as EonxExplicitBoolCompareRector;
use EonX\EasyQuality\Rector\InheritDocRector;
use EonX\EasyQuality\Rector\RestoreDefaultNullToNullableTypeParameterRector;
use EonX\EasyQuality\Rector\SortApiResourceOperationsRector;
use EonX\EasyQuality\Rector\SortConstantsAlphabeticallyRector;
use EonX\EasyQuality\Rector\SortMethodsAlphabeticallyRector;
use EonX\EasyQuality\Rector\SortPropertiesAlphabeticallyRector;
use EonX\EasyQuality\Rector\StrictInArrayRector;
use EonX\EasyQuality\Rector\ValueObject\SortApiResourceOperations;
use EonX\EasyQuality\Rector\ValueObject\SortConstantsAlphabetically;
use EonX\EasyQuality\Rector\ValueObject\SortMethodsAlphabetically;
use EonX\EasyQuality\Rector\ValueObject\SortPropertiesAlphabetically;
use Rector\CodeQuality\Rector\Catch_\ThrowWithPreviousExceptionRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\LogicalAnd\AndAssignsToSeparateLinesRector;
use Rector\CodingStyle\Rector\Assign\SplitDoubleAssignRector;
use Rector\CodingStyle\Rector\FuncCall\StrictArraySearchRector;
use Rector\CodingStyle\Rector\If_\NullableCompareToNullRector;
use Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector;
use Rector\DeadCode\Rector\Array_\RemoveDuplicatedArrayKeyRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\Doctrine\Rector\Class_\InitializeDefaultEntityCollectionRector;
use Rector\DowngradePhp70\Rector\GroupUse\SplitGroupedUseImportsRector;
use Rector\Php71\Rector\ClassConst\PublicConstantVisibilityRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;
use Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Rector\Restoration\Rector\Class_\RemoveFinalFromEntityRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddArrayParamDocTypeRector;
use RectorPrefix20220126\Symplify\SymfonyPhpConfig\ValueObjectInliner;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(AddArrayParamDocTypeRector::class);
    $services->set(AndAssignsToSeparateLinesRector::class);
    $services->set(EonxExplicitBoolCompareRector::class);
    $services->set(ExplicitBoolCompareRector::class);
    $services->set(FinalizeClassesWithoutChildrenRector::class);
    $services->set(InheritDocRector::class);
    $services->set(InitializeDefaultEntityCollectionRector::class);
    $services->set(NullableCompareToNullRector::class);
    $services->set(PublicConstantVisibilityRector::class);
    $services->set(RemoveDuplicatedArrayKeyRector::class);
    $services->set(RemoveFinalFromEntityRector::class);
    $services->set(RemoveNonExistingVarAnnotationRector::class);
    $services->set(RestoreDefaultNullToNullableTypeParameterRector::class);
    $services->set(RestoreDefaultNullToNullableTypePropertyRector::class);
    $services->set(SortApiResourceOperationsRector::class)->call('configure', [
        [
            SortApiResourceOperationsRector::API_RESOURCE_FQCN => 'ApiPlatform\Core\Annotation\ApiResource',
            SortApiResourceOperationsRector::RANKEABLE_CLASS => ValueObjectInliner::inline(
                new SortApiResourceOperations(ApiResourceOperationEasyRankeable::class)
            ),
        ],
    ]);
    $services->set(SortConstantsAlphabeticallyRector::class)->call('configure', [
        [
            SortConstantsAlphabeticallyRector::RANKEABLE_CLASS => ValueObjectInliner::inline(
                new SortConstantsAlphabetically(ClassConstantEasyRankeable::class)
            ),
        ],
    ]);
    $services->set(SortMethodsAlphabeticallyRector::class)->call('configure', [
        [
            SortMethodsAlphabeticallyRector::RANKEABLE_CLASS => ValueObjectInliner::inline(
                new SortMethodsAlphabetically(ClassMethodEasyRankeable::class)
            ),
        ],
    ]);
    $services->set(SortPropertiesAlphabeticallyRector::class)->call('configure', [
        [
            SortPropertiesAlphabeticallyRector::RANKEABLE_CLASS => ValueObjectInliner::inline(
                new SortPropertiesAlphabetically(PropertyEasyRankeable::class)
            ),
        ],
    ]);
    $services->set(SplitDoubleAssignRector::class);
    $services->set(SplitGroupedUseImportsRector::class);
    $services->set(StrictArraySearchRector::class);
    $services->set(StrictInArrayRector::class);
    $services->set(SymplifyQuoteEscapeRector::class);
    $services->set(ThrowWithPreviousExceptionRector::class);
};
