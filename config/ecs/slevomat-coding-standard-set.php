<?php
declare(strict_types=1);

use SlevomatCodingStandard\Sniffs\Arrays\TrailingArrayCommaSniff;
use SlevomatCodingStandard\Sniffs\Classes\ClassConstantVisibilitySniff;
use SlevomatCodingStandard\Sniffs\Classes\EmptyLinesAroundClassBracesSniff;
use SlevomatCodingStandard\Sniffs\Classes\RequireSingleLineMethodSignatureSniff;
use SlevomatCodingStandard\Sniffs\Commenting\DisallowOneLinePropertyDocCommentSniff;
use SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff;
use SlevomatCodingStandard\Sniffs\Commenting\EmptyCommentSniff;
use SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\DisallowEmptySniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\DisallowYodaComparisonSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\NewWithParenthesesSniff;
use SlevomatCodingStandard\Sniffs\Exceptions\DeadCatchSniff;
use SlevomatCodingStandard\Sniffs\Functions\StaticClosureSniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedInheritedVariablePassedToClosureSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\DisallowGroupUseSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\FullyQualifiedClassNameInAnnotationSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\FullyQualifiedGlobalConstantsSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\FullyQualifiedGlobalFunctionsSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\MultipleUsesPerLineSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UnusedUsesSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UseDoesNotStartWithBackslashSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UseSpacingSniff;
use SlevomatCodingStandard\Sniffs\Operators\DisallowEqualOperatorsSniff;
use SlevomatCodingStandard\Sniffs\PHP\UselessSemicolonSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\LongTypeHintsSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\NullableTypeForNullDefaultValueSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\NullTypeHintOnLastPositionSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSpacingSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSpacingSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSpacingSniff;
use SlevomatCodingStandard\Sniffs\Variables\UselessVariableSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(TrailingArrayCommaSniff::class);
    $services->set(ClassConstantVisibilitySniff::class);
    $services->set(EmptyLinesAroundClassBracesSniff::class)
        ->property('linesCountAfterOpeningBrace', 0)
        ->property('linesCountBeforeClosingBrace', 0);
    $services->set(DocCommentSpacingSniff::class)
        ->property('linesCountBetweenAnnotationsGroups', 1)
        ->property('annotationsGroups', [
            '@Annotation',
            '@ApiFilter',
            '@ApiProperty',
            '@ApiResource',
            '@AppAssert\\',
            '@Assert\\',
            '@codeCoverageIgnore',
            '@covers',
            '@coversNothing',
            '@Groups',
            '@method',
            '@ORM\\',
            '@param',
            '@property',
            '@return ',
            '@throws',
            '@noinspection',
            '@phpstan-param',
            '@phpstan-return',
            '@phpstan-template',
            '@phpstan-var',
            '@see',
            '@SuppressWarnings',
            '@UniqueEntity',
            '@var',
        ]);
    $services->set(EmptyCommentSniff::class);
    $services->set(InlineDocCommentDeclarationSniff::class);
    $services->set(DisallowYodaComparisonSniff::class);
    $services->set(NewWithParenthesesSniff::class);
    $services->set(DeadCatchSniff::class);
    $services->set(AlphabeticallySortedUsesSniff::class);
    $services->set(DisallowGroupUseSniff::class);
    $services->set(FullyQualifiedClassNameInAnnotationSniff::class);
    $services->set(FullyQualifiedGlobalConstantsSniff::class);
    $services->set(FullyQualifiedGlobalFunctionsSniff::class);
    $services->set(MultipleUsesPerLineSniff::class);
    $services->set(UnusedUsesSniff::class)
        ->property('searchAnnotations', 1);
    $services->set(UseDoesNotStartWithBackslashSniff::class);
    $services->set(UseSpacingSniff::class)
        ->property('linesCountBeforeFirstUse', 1)
        ->property('linesCountBetweenUseTypes', 0)
        ->property('linesCountAfterLastUse', 1);
    $services->set(DisallowEqualOperatorsSniff::class);
    //$services->set(DeclareStrictTypesSniff::class)
    //    ->property('newlinesCountBetweenOpenTagAndDeclare', 1)
    //    ->property('spacesCountAroundEqualsSign', 0);
    $services->set(LongTypeHintsSniff::class);
    $services->set(NullableTypeForNullDefaultValueSniff::class);
    $services->set(NullTypeHintOnLastPositionSniff::class);
    $services->set(ParameterTypeHintSpacingSniff::class);
    $services->set(PropertyTypeHintSniff::class)
        ->property('enableNativeTypeHint', false);
    $services->set(PropertyTypeHintSpacingSniff::class);
    $services->set(ReturnTypeHintSpacingSniff::class);
    $services->set(StaticClosureSniff::class);
    $services->set(UnusedInheritedVariablePassedToClosureSniff::class);
    $services->set(UselessSemicolonSniff::class);
    $services->set(RequireSingleLineMethodSignatureSniff::class)
        ->property('maxLineLength', 120);
    $services->set(UselessVariableSniff::class);
    $services->set(DisallowOneLinePropertyDocCommentSniff::class);
    $services->set(DisallowEmptySniff::class);
};
