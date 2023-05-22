<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class RequirePublicConstructorSniff implements Sniff
{
    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $method = $phpcsFile->getDeclarationName($stackPtr);

        if ($method !== '__construct') {
            return;
        }

        $properties = $phpcsFile->getMethodProperties($stackPtr);
        if ($properties['scope'] !== 'public') {
            $phpcsFile->addError('Non-public constructors should be avoided', $stackPtr, 'RequirePublicConstructor');
        }
    }

    public function register(): array
    {
        return [\T_FUNCTION];
    }
}
