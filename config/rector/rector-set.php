<?php
declare(strict_types=1);

use Rector\CodeQuality\Rector\Catch_\ThrowWithPreviousExceptionRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\LogicalAnd\AndAssignsToSeparateLinesRector;
use Rector\CodingStyle\Rector\Assign\SplitDoubleAssignRector;
use Rector\CodingStyle\Rector\FuncCall\StrictArraySearchRector;
use Rector\CodingStyle\Rector\If_\NullableCompareToNullRector;
use Rector\CodingStyle\Rector\MethodCall\PreferThisOrSelfMethodCallRector;
use Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector;
use Rector\DeadCode\Rector\Array_\RemoveDuplicatedArrayKeyRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\Doctrine\Rector\Class_\InitializeDefaultEntityCollectionRector;
use Rector\DowngradePhp70\Rector\GroupUse\SplitGroupedUseImportsRector;
use Rector\Php71\Rector\ClassConst\PublicConstantVisibilityRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;
use Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddArrayParamDocTypeRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->set(ThrowWithPreviousExceptionRector::class)
        ->set(ExplicitBoolCompareRector::class)
        ->set(AndAssignsToSeparateLinesRector::class)
        ->set(SplitDoubleAssignRector::class)
        ->set(StrictArraySearchRector::class)
        ->set(NullableCompareToNullRector::class)
        ->set(PreferThisOrSelfMethodCallRector::class)
        ->set(SymplifyQuoteEscapeRector::class)
        ->set(RemoveDuplicatedArrayKeyRector::class)
        ->set(InitializeDefaultEntityCollectionRector::class)
        ->set(PublicConstantVisibilityRector::class)
        ->set(RestoreDefaultNullToNullableTypePropertyRector::class)
        ->set(RemoveNonExistingVarAnnotationRector::class)
        ->set(FinalizeClassesWithoutChildrenRector::class)
        ->set(AddArrayParamDocTypeRector::class)
        ->set(SplitGroupedUseImportsRector::class);
};
