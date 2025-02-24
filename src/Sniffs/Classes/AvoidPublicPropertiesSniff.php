<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use Throwable;

final class AvoidPublicPropertiesSniff extends AbstractVariableSniff
{
    /**
     * @param int $stackPtr
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        try {
            $propertyInfo = $phpcsFile->getMemberProperties($stackPtr);
        } catch (Throwable) {
            return;
        }

        if ($propertyInfo['scope_specified'] === false) {
            $error = 'Visibility must be declared on property "%s"';
            $data = [$tokens[$stackPtr]['content']];

            $phpcsFile->addError($error, $stackPtr, 'ScopeMissing', $data);
        }

        if ($propertyInfo['scope'] === 'public') {
            $error = 'Invalid visibility "public" on property "%s"';
            $data = [$tokens[$stackPtr]['content']];

            $phpcsFile->addError($error, $stackPtr, 'InvalidScope', $data);
        }
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariable(File $phpcsFile, $stackPtr): void
    {
        // Not needed for sniff
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr): void
    {
        // Not needed for sniff
    }
}
