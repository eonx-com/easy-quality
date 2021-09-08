<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\NamespaceHelper;

class MakeClassAbstractSniff implements Sniff
{
    /**
     * @var string
     */
    public const CODE_CLASS_NOT_ABSTRACT = 'ClassNotAbstract';

    /**
     * @var mixed[]
     */
    public $applyTo = [];

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int $clasPointer The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $clasPointer): void
    {
        $tokens = $phpcsFile->getTokens();
        /** @var string $classFqn */
        $classFqn = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $clasPointer);
        $className = $tokens[$clasPointer + 2]['content'];

        $applyToPatterns = $this->getApplyToPatternsForFqn($classFqn);
        $isApplyTo = false;
        foreach ($applyToPatterns as $applyToPattern) {
            if (\preg_match($applyToPattern, $className)) {
                $isApplyTo = true;
            }
        }

        if ($isApplyTo && $phpcsFile->findPrevious(\T_ABSTRACT, $clasPointer) === false) {
            $phpcsFile->addFixableError('Make class abstract', $clasPointer, self::CODE_CLASS_NOT_ABSTRACT);
            $finalTokenPtr = $phpcsFile->findPrevious(\T_FINAL, $clasPointer);
            $phpcsFile->fixer->beginChangeset();
            if ($finalTokenPtr) {
                $phpcsFile->fixer->replaceToken($finalTokenPtr, 'abstract');
            } else {
                $phpcsFile->fixer->addContentBefore($clasPointer, 'abstract ');
            }
            $phpcsFile->fixer->endChangeset();
        }
    }

    /**
     * @param string $classFqn
     *
     * @return string[]
     */
    private function getApplyToPatternsForFqn(string $classFqn): array
    {
        foreach ($this->applyTo as $applyToPattern) {
            if (\preg_match($applyToPattern['namespace'], $classFqn) === 1) {
                return $applyToPattern['patterns'];
            }
        }

        return [];
    }

    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return mixed[]
     */
    public function register()
    {
        return [\T_CLASS];
    }
}
