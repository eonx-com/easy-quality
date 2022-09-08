<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use SlevomatCodingStandard\Helpers\NamespaceHelper;

final class AvoidPrivatePropertiesSniff extends AbstractVariableSniff
{
    /**
     * @var string[]
     */
    public array $applyTo = [
        '/.*/',
    ];

    /**
     * @param int $stackPtr
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr): void
    {
        /** @var string $classFqn */
        $classFqn = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $stackPtr);
        if ($this->shouldApply($classFqn ?? '') === false) {
            return;
        }

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

    private function shouldApply(string $classFqn): bool
    {
        foreach ($this->applyTo as $applyPattern) {
            if (\preg_match($applyPattern, $classFqn) === 1) {
                return true;
            }
        }

        return false;
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
