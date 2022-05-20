<?php
declare(strict_types=1);

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
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use EonX\EasyQuality\Sniffs\Attributes\DoctrineColumnTypeSniff;
use EonX\EasyQuality\Sniffs\Classes\PropertyTypeSniff;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(SetList::PSR_12);

    $services = $containerConfigurator->services();

    $services->set(AlphabeticallySortedUsesSniff::class);
    $services->set(BlankLineAfterNamespaceFixer::class);
    $services->set(BlankLineBeforeStatementFixer::class)
        ->call('configure', [
            [
                'statements' => [
                    'break',
                    'continue',
                    'return',
                    'throw',
                    'try',
                ],
            ],
        ]);
    $services->set(CamelCapsMethodNameSniff::class);
    $services->set(CastSpacesFixer::class)
        ->call('configure', [['space' => 'none']]);
    $services->set(ClassAttributesSeparationFixer::class)
        ->call('configure', [['elements' => ['const' => 'one', 'method' => 'one', 'property' => 'one']]]);
    $services->set(ClassConstantVisibilitySniff::class);
    $services->set(ConcatSpaceFixer::class)
        ->call('configure', [['spacing' => 'one']]);
    $services->set(DeadCatchSniff::class);
    $services->set(DeclareEqualNormalizeFixer::class)
        ->call('configure', [['space' => 'none']]);
    $services->set(DisallowEmptySniff::class);
    $services->set(DisallowEqualOperatorsSniff::class);
    $services->set(DisallowGroupUseSniff::class);
    $services->set(DisallowLongArraySyntaxSniff::class);
    $services->set(DisallowNonNullDefaultValueSniff::class);
    $services->set(DisallowOneLinePropertyDocCommentSniff::class);
    $services->set(DisallowShortOpenTagSniff::class);
    $services->set(DisallowTrailingCommaInCallSniff::class);
    $services->set(DisallowTrailingCommaInClosureUseSniff::class);
    $services->set(DisallowTrailingCommaInDeclarationSniff::class);
    $services->set(DisallowYodaComparisonSniff::class);
    $services->set(DoctrineColumnTypeSniff::class)
        ->property('replacePairs', [
            'date' => 'date_immutable',
            'datetime' => 'datetime_immutable',
        ]);
    $services->set(EmptyCommentSniff::class);
    $services->set(EmptyLinesAroundClassBracesSniff::class)
        ->property('linesCountAfterOpeningBrace', 0)
        ->property('linesCountBeforeClosingBrace', 0);
    $services->set(FullyQualifiedClassNameInAnnotationSniff::class);
    $services->set(FullyQualifiedGlobalConstantsSniff::class);
    $services->set(FullyQualifiedGlobalFunctionsSniff::class);
    $services->set(InlineDocCommentDeclarationSniff::class);
    $services->set(LinebreakAfterOpeningTagFixer::class);
    $services->set(LineLengthSniff::class)
        ->property('absoluteLineLimit', 120)
        ->property('ignoreComments', true);
    $services->set(LongTypeHintsSniff::class);
    $services->set(MultilineWhitespaceBeforeSemicolonsFixer::class)
        ->call('configure', [['strategy' => 'no_multi_line']]);
    $services->set(MultipleUsesPerLineSniff::class);
    $services->set(NewWithParenthesesSniff::class);
    $services->set(NoBlankLinesAfterClassOpeningFixer::class);
    $services->set(NoNotOperatorSniff::class);
    $services->set(NullableTypeForNullDefaultValueSniff::class);
    $services->set(NullTypeHintOnLastPositionSniff::class);
    $services->set(OrderedClassElementsFixer::class);
    $services->set(ParameterTypeHintSpacingSniff::class);
    $services->set(PhpdocAddMissingParamAnnotationFixer::class)
        ->call('configure', [['only_untyped' => true]]);
    $services->set(PhpdocAlignFixer::class)
        ->call('configure', [['align' => 'left']]);
    $services->set(PhpdocSeparationFixer::class);
    $services->set(PropertyTypeSniff::class)
        ->property('replacePairs', [
            'DateTime' => 'DateTimeImmutable',
            'Carbon' => 'CarbonImmutable',
        ]);
    $services->set(PropertyTypeHintSniff::class)
        ->property('enableNativeTypeHint', false);
    $services->set(PropertyTypeHintSpacingSniff::class);
    $services->set(Psr4Sniff::class);
    $services->set(RequirePublicConstructorSniff::class);
    $services->set(RequireStrictDeclarationSniff::class);
    $services->set(ReturnTypeHintSpacingSniff::class);
    $services->set(SingleBlankLineBeforeNamespaceFixer::class);
    $services->set(StrictDeclarationFormatSniff::class);
    $services->set(SuperfluousWhitespaceSniff::class);
    $services->set(TrailingArrayCommaSniff::class);
    $services->set(TrailingCommaInMultilineFixer::class);
    $services->set(UnusedInheritedVariablePassedToClosureSniff::class);
    $services->set(UnusedUsesSniff::class)
        ->property('searchAnnotations', 1);
    $services->set(UseDoesNotStartWithBackslashSniff::class);
    $services->set(UselessSemicolonSniff::class);
    $services->set(UselessVariableSniff::class);
    $services->set(UseSpacingSniff::class)
        ->property('linesCountBeforeFirstUse', 1)
        ->property('linesCountBetweenUseTypes', 0)
        ->property('linesCountAfterLastUse', 1);
};
