<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Attributes\DoctrineColumnTypeSniff;
use EonX\EasyQuality\Sniffs\Classes\PropertyTypeSniff;
use EonX\EasyQuality\Sniffs\Classes\RequirePublicConstructorSniff;
use EonX\EasyQuality\Sniffs\Classes\RequireStrictDeclarationSniff;
use EonX\EasyQuality\Sniffs\Classes\StrictDeclarationFormatSniff;
use EonX\EasyQuality\Sniffs\ControlStructures\NoNotOperatorSniff;
use EonX\EasyQuality\Sniffs\Functions\DisallowNonNullDefaultValueSniff;
use EonX\EasyQuality\Sniffs\Namespaces\Psr4Sniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DisallowShortOpenTagSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAddMissingParamAnnotationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;
use PhpCsFixer\Fixer\PhpTag\LinebreakAfterOpeningTagFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use SlevomatCodingStandard\Sniffs\Arrays\TrailingArrayCommaSniff;
use SlevomatCodingStandard\Sniffs\Classes\ClassConstantVisibilitySniff;
use SlevomatCodingStandard\Sniffs\Classes\EmptyLinesAroundClassBracesSniff;
use SlevomatCodingStandard\Sniffs\Commenting\DisallowOneLinePropertyDocCommentSniff;
use SlevomatCodingStandard\Sniffs\Commenting\EmptyCommentSniff;
use SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\DisallowEmptySniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\DisallowYodaComparisonSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\NewWithParenthesesSniff;
use SlevomatCodingStandard\Sniffs\Exceptions\DeadCatchSniff;
use SlevomatCodingStandard\Sniffs\Functions\DisallowTrailingCommaInCallSniff;
use SlevomatCodingStandard\Sniffs\Functions\DisallowTrailingCommaInClosureUseSniff;
use SlevomatCodingStandard\Sniffs\Functions\DisallowTrailingCommaInDeclarationSniff;
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
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->sets([
        SetList::PSR_12,
    ]);

    $ecsConfig->rule(AlphabeticallySortedUsesSniff::class);
    $ecsConfig->rule(BlankLineAfterNamespaceFixer::class);
    $ecsConfig->ruleWithConfiguration(BlankLineBeforeStatementFixer::class, [
        'statements' => [
            'break',
            'continue',
            'return',
            'throw',
            'try',
        ],
    ]);
    $ecsConfig->rule(CamelCapsMethodNameSniff::class);
    $ecsConfig->ruleWithConfiguration(CastSpacesFixer::class, [
        'space' => 'none',
    ]);
    $ecsConfig->ruleWithConfiguration(ClassAttributesSeparationFixer::class, [
        'elements' => [
            'const' => 'one',
            'method' => 'one',
            'property' => 'one',
        ]
    ]);
    $ecsConfig->rule(ClassConstantVisibilitySniff::class);
    $ecsConfig->ruleWithConfiguration(ConcatSpaceFixer::class, [
        'spacing' => 'one',
    ]);
    $ecsConfig->rule(DeadCatchSniff::class);
    $ecsConfig->ruleWithConfiguration(DeclareEqualNormalizeFixer::class, [
        'space' => 'none',
    ]);
    $ecsConfig->rule(DisallowEmptySniff::class);
    $ecsConfig->rule(DisallowEqualOperatorsSniff::class);
    $ecsConfig->rule(DisallowGroupUseSniff::class);
    $ecsConfig->rule(DisallowLongArraySyntaxSniff::class);
    $ecsConfig->rule(DisallowNonNullDefaultValueSniff::class);
    $ecsConfig->rule(DisallowOneLinePropertyDocCommentSniff::class);
    $ecsConfig->rule(DisallowShortOpenTagSniff::class);
    $ecsConfig->ruleWithConfiguration(DisallowTrailingCommaInCallSniff::class, [
        'onlySingleLine' => true
    ]);
    $ecsConfig->ruleWithConfiguration(DisallowTrailingCommaInClosureUseSniff::class, [
        'onlySingleLine' => true
    ]);
    $ecsConfig->ruleWithConfiguration(DisallowTrailingCommaInDeclarationSniff::class, [
        'onlySingleLine' => true
    ]);
    $ecsConfig->rule(DisallowYodaComparisonSniff::class);
    $ecsConfig->ruleWithConfiguration(DoctrineColumnTypeSniff::class, [
        'replacePairs' => [
            'date' => 'date_immutable',
            'datetime' => 'datetime_immutable',
        ]
    ]);
    $ecsConfig->rule(EmptyCommentSniff::class);
    $ecsConfig->ruleWithConfiguration(EmptyLinesAroundClassBracesSniff::class, [
        'linesCountAfterOpeningBrace' => 0,
        'linesCountBeforeClosingBrace' => 0,
    ]);
    $ecsConfig->rule(FullyQualifiedClassNameInAnnotationSniff::class);
    $ecsConfig->rule(FullyQualifiedGlobalConstantsSniff::class);
    $ecsConfig->rule(FullyQualifiedGlobalFunctionsSniff::class);
    $ecsConfig->rule(InlineDocCommentDeclarationSniff::class);
    $ecsConfig->rule(LinebreakAfterOpeningTagFixer::class);
    $ecsConfig->ruleWithConfiguration(LineLengthSniff::class, [
        'absoluteLineLimit' => 120,
        'ignoreComments' => true,
    ]);
    $ecsConfig->rule(LongTypeHintsSniff::class);
    $ecsConfig->ruleWithConfiguration(MultilineWhitespaceBeforeSemicolonsFixer::class, [
        'strategy' => 'no_multi_line',
    ]);
    $ecsConfig->rule(MultipleUsesPerLineSniff::class);
    $ecsConfig->rule(NewWithParenthesesSniff::class);
    $ecsConfig->rule(NoBlankLinesAfterClassOpeningFixer::class);
    $ecsConfig->rule(NoNotOperatorSniff::class);
    $ecsConfig->rule(NullableTypeForNullDefaultValueSniff::class);
    $ecsConfig->rule(NullTypeHintOnLastPositionSniff::class);
    $ecsConfig->rule(OrderedClassElementsFixer::class);
    $ecsConfig->rule(ParameterTypeHintSpacingSniff::class);
    $ecsConfig->ruleWithConfiguration(PhpdocAddMissingParamAnnotationFixer::class, [
        'only_untyped' => true,
    ]);
    $ecsConfig->ruleWithConfiguration(PhpdocAlignFixer::class, [
        'align' => 'left',
    ]);
    $ecsConfig->rule(PhpdocSeparationFixer::class);
    $ecsConfig->ruleWithConfiguration(PropertyTypeSniff::class, [
        'replacePairs' => [
            'DateTime' => 'DateTimeImmutable',
            'Carbon' => 'CarbonImmutable',
        ]
    ]);
    $ecsConfig->ruleWithConfiguration(PropertyTypeHintSniff::class, [
        'enableNativeTypeHint' => false,
    ]);
    $ecsConfig->rule(PropertyTypeHintSpacingSniff::class);
    $ecsConfig->rule(Psr4Sniff::class);
    $ecsConfig->rule(RequirePublicConstructorSniff::class);
    $ecsConfig->rule(RequireStrictDeclarationSniff::class);
    $ecsConfig->rule(ReturnTypeHintSpacingSniff::class);
    $ecsConfig->rule(SingleBlankLineBeforeNamespaceFixer::class);
    $ecsConfig->rule(StrictDeclarationFormatSniff::class);
    $ecsConfig->rule(SuperfluousWhitespaceSniff::class);
    $ecsConfig->rule(TrailingArrayCommaSniff::class);
    $ecsConfig->rule(TrailingCommaInMultilineFixer::class);
    $ecsConfig->rule(UnusedInheritedVariablePassedToClosureSniff::class);
    $ecsConfig->ruleWithConfiguration(UnusedUsesSniff::class, [
        'searchAnnotations' => 1,
    ]);
    $ecsConfig->rule(UseDoesNotStartWithBackslashSniff::class);
    $ecsConfig->rule(UselessSemicolonSniff::class);
    $ecsConfig->rule(UselessVariableSniff::class);
    $ecsConfig->ruleWithConfiguration(UseSpacingSniff::class, [
        'linesCountBeforeFirstUse' => 1,
        'linesCountBetweenUseTypes' => 0,
        'linesCountAfterLastUse' => 1,
    ]);
};
