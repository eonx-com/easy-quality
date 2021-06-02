<?php
declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAddMissingParamAnnotationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;
use PhpCsFixer\Fixer\PhpTag\LinebreakAfterOpeningTagFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(PhpdocSeparationFixer::class);
    $services->set(NoBlankLinesAfterClassOpeningFixer::class);
    $services->set(SingleBlankLineBeforeNamespaceFixer::class);
    $services->set(BlankLineAfterNamespaceFixer::class);
    $services->set(ClassAttributesSeparationFixer::class)
        ->call('configure', [['elements' => ['const', 'method', 'property']]]);
    $services->set(MultilineWhitespaceBeforeSemicolonsFixer::class)
        ->call('configure', [['strategy' => 'no_multi_line']]);
    $services->set(CastSpacesFixer::class)
        ->call('configure', [['space' => 'none']]);
    $services->set(ConcatSpaceFixer::class)
        ->call('configure', [['spacing' => 'one']]);
    $services->set(PhpdocAlignFixer::class)
        ->tag('param')
        ->tag('property')
        ->tag('return ')
        ->tag('throws')
        ->tag('type')
        ->tag('var')
        ->tag('method')
        ->call('configure', [['align' => 'left']]);
    $services->set(BlankLineBeforeStatementFixer::class)
        ->tag('break')
        ->tag('continue')
        ->tag('return')
        ->tag('throw')
        ->tag('try');
    $services->set(LinebreakAfterOpeningTagFixer::class);
    $services->set(PhpdocAddMissingParamAnnotationFixer::class)
        ->call('configure', [['only_untyped' => true]]);
};
