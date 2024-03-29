<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class NoNotOperatorSniff implements Sniff
{
    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $phpcsFile->addError('Strict comparision operator should be used instead', $stackPtr, 'NoNotOperator');
    }

    public function register(): array
    {
        return [\T_BOOLEAN_NOT, \T_IS_NOT_EQUAL];
    }
}
