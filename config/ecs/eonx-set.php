<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Attributes\DoctrineColumnTypeSniff;
use EonX\EasyQuality\Sniffs\Classes\PropertyTypeSniff;
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
use PhpCsFixer\Fixer\ClassNotation\FinalClassFixer;
use PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLinesBeforeNamespaceFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAddMissingParamAnnotationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTagCasingFixer;
use PhpCsFixer\Fixer\PhpTag\LinebreakAfterOpeningTagFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use SlevomatCodingStandard\Sniffs\Arrays\TrailingArrayCommaSniff;
use SlevomatCodingStandard\Sniffs\Classes\ClassConstantVisibilitySniff;
use SlevomatCodingStandard\Sniffs\Classes\EmptyLinesAroundClassBracesSniff;
use SlevomatCodingStandard\Sniffs\Classes\EnumCaseSpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\PropertyDeclarationSniff;
use SlevomatCodingStandard\Sniffs\Classes\TraitUseDeclarationSniff;
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
use SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\LongTypeHintsSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\NullableTypeForNullDefaultValueSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\NullTypeHintOnLastPositionSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSpacingSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSpacingSniff;
use SlevomatCodingStandard\Sniffs\Variables\UselessVariableSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPreparedSets(psr12: true)
    ->withRules([
        AlphabeticallySortedUsesSniff::class,
        BlankLineAfterNamespaceFixer::class,
        CamelCapsMethodNameSniff::class,
        ClassConstantVisibilitySniff::class,
        DeadCatchSniff::class,
        DisallowEmptySniff::class,
        DisallowEqualOperatorsSniff::class,
        DisallowGroupUseSniff::class,
        DisallowLongArraySyntaxSniff::class,
        DisallowMixedTypeHintSniff::class,
        DisallowNonNullDefaultValueSniff::class,
        DisallowOneLinePropertyDocCommentSniff::class,
        DisallowShortOpenTagSniff::class,
        DisallowYodaComparisonSniff::class,
        EmptyCommentSniff::class,
        FinalClassFixer::class,
        FullyQualifiedClassNameInAnnotationSniff::class,
        FullyQualifiedGlobalConstantsSniff::class,
        FullyQualifiedGlobalFunctionsSniff::class,
        InlineDocCommentDeclarationSniff::class,
        LinebreakAfterOpeningTagFixer::class,
        LongTypeHintsSniff::class,
        MultipleUsesPerLineSniff::class,
        NewWithParenthesesSniff::class,
        NoBlankLinesAfterClassOpeningFixer::class,
        NoNotOperatorSniff::class,
        NullTypeHintOnLastPositionSniff::class,
        NullableTypeForNullDefaultValueSniff::class,
        ParameterTypeHintSpacingSniff::class,
        PhpdocSeparationFixer::class,
        PropertyDeclarationSniff::class,
        PropertyTypeHintSniff::class,
        Psr4Sniff::class,
        RequireStrictDeclarationSniff::class,
        ReturnTypeHintSpacingSniff::class,
        SingleQuoteFixer::class,
        StrictDeclarationFormatSniff::class,
        StrictParamFixer::class,
        SuperfluousWhitespaceSniff::class,
        TrailingArrayCommaSniff::class,
        TrailingCommaInMultilineFixer::class,
        TraitUseDeclarationSniff::class,
        UnusedInheritedVariablePassedToClosureSniff::class,
        UseDoesNotStartWithBackslashSniff::class,
        UselessSemicolonSniff::class,
        UselessVariableSniff::class,
    ])
    ->withConfiguredRule(BlankLinesBeforeNamespaceFixer::class, [
        'max_line_breaks' => 2,
        'min_line_breaks' => 2,
    ])
    ->withConfiguredRule(BlankLineBeforeStatementFixer::class, [
        'statements' => [
            'break',
            'continue',
            'return',
            'throw',
            'try',
        ],
    ])
    ->withConfiguredRule(CastSpacesFixer::class, [
        'space' => 'none',
    ])
    ->withConfiguredRule(ClassAttributesSeparationFixer::class, [
        'elements' => [
            'const' => 'one',
            'method' => 'one',
            'property' => 'one',
        ],
    ])
    ->withConfiguredRule(ConcatSpaceFixer::class, [
        'spacing' => 'one',
    ])
    ->withConfiguredRule(DeclareEqualNormalizeFixer::class, [
        'space' => 'none',
    ])
    ->withConfiguredRule(DisallowTrailingCommaInCallSniff::class, [
        'onlySingleLine' => true,
    ])
    ->withConfiguredRule(DisallowTrailingCommaInClosureUseSniff::class, [
        'onlySingleLine' => true,
    ])
    ->withConfiguredRule(DisallowTrailingCommaInDeclarationSniff::class, [
        'onlySingleLine' => true,
    ])
    ->withConfiguredRule(DoctrineColumnTypeSniff::class, [
        'replacePairs' => [
            'date' => 'date_immutable',
            'datetime' => 'datetime_immutable',
        ],
    ])
    ->withConfiguredRule(EnumCaseSpacingSniff::class, [
        'minLinesCountBeforeWithoutComment' => 1,
    ])
    ->withConfiguredRule(EmptyLinesAroundClassBracesSniff::class, [
        'linesCountAfterOpeningBrace' => 0,
        'linesCountBeforeClosingBrace' => 0,
    ])
    ->withConfiguredRule(LineLengthSniff::class, [
        'absoluteLineLimit' => 120,
        'ignoreComments' => true,
    ])
    ->withConfiguredRule(MultilineWhitespaceBeforeSemicolonsFixer::class, [
        'strategy' => 'no_multi_line',
    ])
    ->withConfiguredRule(OrderedClassElementsFixer::class, [
        'case_sensitive' => true,
        'order' => [
            'use_trait',
            'case',
            'constant_public',
            'constant_protected',
            'constant_private',
            'property_public_static',
            'property_public',
            'property_protected_static',
            'property_protected',
            'property_private_static',
            'property_private',
            'construct',
            'destruct',
            'magic',
            'phpunit',
            'method_public_abstract_static',
            'method_public_static',
            'method_public_abstract',
            'method_public',
            'method_protected_abstract_static',
            'method_protected_static',
            'method_protected_abstract',
            'method_protected',
            'method_private_abstract_static',
            'method_private_static',
            'method_private_abstract',
            'method_private',
        ],
        'sort_algorithm' => OrderedClassElementsFixer::SORT_ALPHA,
    ])
    ->withConfiguredRule(PhpdocAddMissingParamAnnotationFixer::class, [
        'only_untyped' => true,
    ])
    ->withConfiguredRule(PhpdocAlignFixer::class, [
        'align' => 'left',
    ])
    ->withConfiguredRule(PhpdocTagCasingFixer::class, [
        'tags' => [
            'inheritdoc',
        ],
    ])
    ->withConfiguredRule(PropertyTypeSniff::class, [
        'replacePairs' => [
            'Carbon' => 'CarbonImmutable',
            'DateTime' => 'DateTimeImmutable',
        ],
    ])
    ->withConfiguredRule(UnusedUsesSniff::class, [
        'searchAnnotations' => 1,
    ])
    ->withConfiguredRule(UseSpacingSniff::class, [
        'linesCountAfterLastUse' => 1,
        'linesCountBeforeFirstUse' => 1,
        'linesCountBetweenUseTypes' => 0,
    ]);
