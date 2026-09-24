<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class DisallowElseSniff implements Sniff
{
    public const string CODE_ELSE_FOUND = 'ElseFound';

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $phpcsFile->addError(
            'Avoid else/elseif, restructure with an early exit (return/continue/break) or a match expression.',
            $stackPtr,
            self::CODE_ELSE_FOUND,
        );
    }

    /**
     * @return list<int|string>
     */
    public function register(): array
    {
        return [\T_ELSE, \T_ELSEIF];
    }
}
