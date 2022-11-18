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

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        /** @var string $classFqn */
        $classFqn = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $stackPtr);
        $className = $tokens[$stackPtr + 2]['content'];

        $applyToPatterns = $this->getApplyToPatternsForFqn($classFqn);
        $isApplyTo = false;
        foreach ($applyToPatterns as $applyToPattern) {
            if (\preg_match($applyToPattern, $className)) {
                $isApplyTo = true;
            }
        }

        if ($isApplyTo && $phpcsFile->findPrevious(\T_ABSTRACT, $stackPtr) === false) {
            $phpcsFile->addFixableError('Make class abstract', $stackPtr, self::CODE_CLASS_NOT_ABSTRACT);
            $finalTokenPtr = $phpcsFile->findPrevious(\T_FINAL, $stackPtr);
            $phpcsFile->fixer->beginChangeset();
            if ($finalTokenPtr) {
                $phpcsFile->fixer->replaceToken($finalTokenPtr, 'abstract');
            } else {
                $phpcsFile->fixer->addContentBefore($stackPtr, 'abstract ');
            }
            $phpcsFile->fixer->endChangeset();
        }
    }

    /**
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

    public function register()
    {
        return [\T_CLASS];
    }
}
