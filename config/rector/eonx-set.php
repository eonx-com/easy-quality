<?php
declare(strict_types=1);

use EonX\EasyQuality\Rector\ExplicitBoolCompareRector as EonxExplicitBoolCompareRector;
use EonX\EasyQuality\Rector\InheritDocRector;
use EonX\EasyQuality\Rector\RestoreDefaultNullToNullableTypeParameterRector;
use EonX\EasyQuality\Rector\StrictInArrayRector;
use Rector\CodeQuality\Rector\Catch_\ThrowWithPreviousExceptionRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\LogicalAnd\AndAssignsToSeparateLinesRector;
use Rector\CodingStyle\Rector\Assign\SplitDoubleAssignRector;
use Rector\CodingStyle\Rector\FuncCall\StrictArraySearchRector;
use Rector\CodingStyle\Rector\If_\NullableCompareToNullRector;
use Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Array_\RemoveDuplicatedArrayKeyRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\Doctrine\Rector\Class_\InitializeDefaultEntityCollectionRector;
use Rector\DowngradePhp70\Rector\GroupUse\SplitGroupedUseImportsRector;
use Rector\Php71\Rector\ClassConst\PublicConstantVisibilityRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;
use Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Rector\Restoration\Rector\Class_\RemoveFinalFromEntityRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddArrayParamDocTypeRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(AddArrayParamDocTypeRector::class);
    $rectorConfig->rule(AndAssignsToSeparateLinesRector::class);
    $rectorConfig->rule(EonxExplicitBoolCompareRector::class);
    $rectorConfig->rule(ExplicitBoolCompareRector::class);
    $rectorConfig->rule(FinalizeClassesWithoutChildrenRector::class);
    $rectorConfig->rule(InheritDocRector::class);
    $rectorConfig->rule(InitializeDefaultEntityCollectionRector::class);
    $rectorConfig->rule(NullableCompareToNullRector::class);
    $rectorConfig->rule(PublicConstantVisibilityRector::class);
    $rectorConfig->rule(RemoveDuplicatedArrayKeyRector::class);
    $rectorConfig->rule(RemoveFinalFromEntityRector::class);
    $rectorConfig->rule(RemoveNonExistingVarAnnotationRector::class);
    $rectorConfig->rule(RestoreDefaultNullToNullableTypeParameterRector::class);
    $rectorConfig->rule(RestoreDefaultNullToNullableTypePropertyRector::class);
    $rectorConfig->rule(SplitDoubleAssignRector::class);
    $rectorConfig->rule(SplitGroupedUseImportsRector::class);
    $rectorConfig->rule(StrictArraySearchRector::class);
    $rectorConfig->rule(StrictInArrayRector::class);
    $rectorConfig->rule(SymplifyQuoteEscapeRector::class);
    $rectorConfig->rule(ThrowWithPreviousExceptionRector::class);
};
