<?php
declare(strict_types=1);

use EonX\EasyQuality\Rector\ExplicitBoolCompareRector as EonxExplicitBoolCompareRector;
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
use Rector\Doctrine\CodeQuality\Rector\Class_\ExplicitRelationCollectionRector;
use Rector\Php71\Rector\ClassConst\PublicConstantVisibilityRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;
use Rector\TypeDeclaration\Rector\ArrowFunction\AddArrowFunctionReturnTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedCallRector;

return RectorConfig::configure()
    ->withRules([
        AddArrowFunctionReturnTypeRector::class,
        ReturnTypeFromStrictTypedCallRector::class,
        AndAssignsToSeparateLinesRector::class,
        EonxExplicitBoolCompareRector::class,
        ExplicitBoolCompareRector::class,
        ExplicitRelationCollectionRector::class,
        NullableCompareToNullRector::class,
        PublicConstantVisibilityRector::class,
        RemoveDuplicatedArrayKeyRector::class,
        RemoveNonExistingVarAnnotationRector::class,
        RestoreDefaultNullToNullableTypePropertyRector::class,
        SplitDoubleAssignRector::class,
        StrictArraySearchRector::class,
        SymplifyQuoteEscapeRector::class,
        ThrowWithPreviousExceptionRector::class,
    ]);
