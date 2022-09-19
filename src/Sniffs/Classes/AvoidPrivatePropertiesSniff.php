<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;

/**
 * @deprecated since 3.0.0, will be removed in 4.0.0
 */
final class AvoidPrivatePropertiesSniff extends AbstractVariableSniff
{
    /**
     * @param int $stackPtr
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        try {
            $propertyInfo = $phpcsFile->getMemberProperties($stackPtr);

            if (empty($propertyInfo)) {
                return;
            }
        } catch (\Throwable $exception) {
            return;
        }

        if (($propertyInfo['scope_specified'] ?? false) === false || empty($propertyInfo['scope'])) {
            $error = 'Visibility must be declared on property "%s"';
            $data = [$tokens[$stackPtr]['content']] ?? [];

            $phpcsFile->addError($error, $stackPtr, 'ScopeMissing', $data);
        }

        if ($propertyInfo['scope'] === 'private') {
            $error = 'Invalid visibility "private" on property "%s"';
            $data = [$tokens[$stackPtr]['content']] ?? [];

            $phpcsFile->addError($error, $stackPtr, 'InvalidScope', $data);
        }
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariable(File $phpcsFile, $stackPtr): void
    {
        // No needed for sniff
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr): void
    {
        // No needed for sniff
    }
}
