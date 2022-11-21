<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Exceptions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\TokenHelper;

/**
 * @deprecated since 3.0.0, will be removed in 4.0.0. Use \EonX\EasyQuality\PHPStan\ThrowExceptionMessageRule instead.
 */
final class ThrowExceptionMessageSniff implements Sniff
{
    /**
     * @var string[]
     */
    public $validPrefixes = ['exceptions.'];

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        $nextTokenPosition = TokenHelper::findNextEffective($phpcsFile, $stackPtr + 1);
        $nextToken = $tokens[$nextTokenPosition];

        if ($nextToken['code'] !== \T_NEW) {
            return;
        }

        $openParenthesisToken = TokenHelper::findNext($phpcsFile, [\T_OPEN_PARENTHESIS], $stackPtr);

        $messageTokenPosition = TokenHelper::findNextEffective($phpcsFile, $openParenthesisToken + 1);
        $messageToken = $tokens[$messageTokenPosition];

        if (\in_array($messageToken['code'], Tokens::$stringTokens, true) === false) {
            return;
        }

        if ($this->startsWithValidPrefix(\trim((string)$messageToken['content'], "'\""))) {
            return;
        }

        $phpcsFile->addErrorOnLine(
            \sprintf(
                'Exception message must be either a variable or a translation message, started with one of [%s]',
                \implode(', ', $this->validPrefixes)
            ),
            $tokens[$stackPtr]['line'],
            'ThrowExceptionMessageSniff'
        );
    }

    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return int[]
     */
    public function register(): array
    {
        return [\T_THROW];
    }

    /**
     * Does the message start with valid prefix.
     */
    private function startsWithValidPrefix(string $message): bool
    {
        foreach ($this->validPrefixes as $validPrefix) {
            if (\str_starts_with($message, $validPrefix)) {
                return true;
            }
        }

        return false;
    }
}
