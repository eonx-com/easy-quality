<?php
declare(strict_types=1);

use EonX\EasyQuality\Rector\ExplicitBoolCompareRector as EonxExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\Catch_\ThrowWithPreviousExceptionRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\LogicalAnd\AndAssignsToSeparateLinesRector;
use Rector\CodingStyle\Rector\Assign\SplitDoubleAssignRector;
use Rector\CodingStyle\Rector\FuncCall\FunctionFirstClassCallableRector;
use Rector\CodingStyle\Rector\FuncCall\StrictArraySearchRector;
use Rector\CodingStyle\Rector\If_\NullableCompareToNullRector;
use Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Array_\RemoveDuplicatedArrayKeyRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\Doctrine\TypedCollections\Rector\Class_\InitializeCollectionInConstructorRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;

return RectorConfig::configure()
    ->withPreparedSets(typeDeclarations: true)
    ->withRules([
        AndAssignsToSeparateLinesRector::class,
        EonxExplicitBoolCompareRector::class,
        ExplicitBoolCompareRector::class,
        FunctionFirstClassCallableRector::class,
        InitializeCollectionInConstructorRector::class,
        NullableCompareToNullRector::class,
        RemoveDuplicatedArrayKeyRector::class,
        RemoveNonExistingVarAnnotationRector::class,
        RestoreDefaultNullToNullableTypePropertyRector::class,
        SplitDoubleAssignRector::class,
        StrictArraySearchRector::class,
        SymplifyQuoteEscapeRector::class,
        ThrowWithPreviousExceptionRector::class,
    ]);
