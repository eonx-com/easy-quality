<?php
declare(strict_types=1);

use EonX\EasyQuality\Rector\ExplicitBoolCompareRector as EonxExplicitBoolCompareRector;
use EonX\EasyQuality\Rector\InheritDocRector;
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
use Rector\Php71\Rector\ClassConst\PublicConstantVisibilityRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;
use Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Rector\TypeDeclaration\Rector\ArrowFunction\AddArrowFunctionReturnTypeRector;
use Rector\TypeDeclaration\Rector\Closure\AddClosureReturnTypeRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(AddArrowFunctionReturnTypeRector::class);
    $rectorConfig->rule(AddClosureReturnTypeRector::class);
    $rectorConfig->rule(AndAssignsToSeparateLinesRector::class);
    $rectorConfig->rule(EonxExplicitBoolCompareRector::class);
    $rectorConfig->rule(ExplicitBoolCompareRector::class);
    $rectorConfig->rule(FinalizeClassesWithoutChildrenRector::class);
    $rectorConfig->rule(InheritDocRector::class);
    $rectorConfig->rule(InitializeDefaultEntityCollectionRector::class);
    $rectorConfig->rule(NullableCompareToNullRector::class);
    $rectorConfig->rule(PublicConstantVisibilityRector::class);
    $rectorConfig->rule(RemoveDuplicatedArrayKeyRector::class);
    $rectorConfig->rule(RemoveNonExistingVarAnnotationRector::class);
    $rectorConfig->rule(RestoreDefaultNullToNullableTypePropertyRector::class);
    $rectorConfig->rule(SplitDoubleAssignRector::class);
    $rectorConfig->rule(StrictArraySearchRector::class);
    $rectorConfig->rule(StrictInArrayRector::class);
    $rectorConfig->rule(SymplifyQuoteEscapeRector::class);
    $rectorConfig->rule(ThrowWithPreviousExceptionRector::class);
};
