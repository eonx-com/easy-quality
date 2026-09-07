<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Copy of \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff
 * with additional `ignoreConstants` and `ignoreEnums` options.
 */
final class LineLengthSniff implements Sniff
{
    /**
     * The limit that the length of a line must not exceed. Set to zero (0) to disable.
     */
    public int $absoluteLineLimit = 100;

    /**
     * Whether or not to ignore trailing comments.
     * This has the effect of also ignoring all lines that only contain comments.
     */
    public bool $ignoreComments = false;

    /**
     * Whether or not to ignore lines with constant declarations (`const FOO = ...`)
     * or constant references (`Foo::BAR`, i.e. UPPER_CASE member name).
     */
    public bool $ignoreConstants = false;

    /**
     * Whether or not to ignore lines with enum case declarations (`case Foo = ...`)
     * or enum case references (`Foo::Bar`, i.e. non-UPPER_CASE member name that is not a method call).
     */
    public bool $ignoreEnums = false;

    /**
     * Whether or not to ignore lines with static method calls (`Foo::method()`).
     */
    public bool $ignoreStaticMethods = false;

    /**
     * The limit that the length of a line should not exceed.
     */
    public int $lineLimit = 80;

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();
        for ($i = 1; $i < $phpcsFile->numTokens; $i++) {
            if ($tokens[$i]['column'] === 1) {
                $this->checkLineLength($phpcsFile, $i);
            }
        }

        $this->checkLineLength($phpcsFile, $phpcsFile->numTokens);

        // Ignore the rest of the file
        return $phpcsFile->numTokens;
    }

    public function register(): array
    {
        return [\T_OPEN_TAG];
    }

    /**
     * @param int $stackPtr The first token on the next line
     */
    private function checkLineLength(File $phpcsFile, int $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        // The passed token is the first on the line
        $stackPtr--;

        if ($tokens[$stackPtr]['column'] === 1 && $tokens[$stackPtr]['length'] === 0) {
            // Blank line
            return;
        }

        if ($tokens[$stackPtr]['column'] !== 1 && $tokens[$stackPtr]['content'] === $phpcsFile->eolChar) {
            $stackPtr--;
        }

        $onlyComment = false;
        if (isset(Tokens::COMMENT_TOKENS[$tokens[$stackPtr]['code']])) {
            $prevNonWhiteSpace = $phpcsFile->findPrevious(Tokens::EMPTY_TOKENS, $stackPtr - 1, null, true);
            if ($tokens[$stackPtr]['line'] !== $tokens[$prevNonWhiteSpace]['line']) {
                $onlyComment = true;
            }
        }

        if ($onlyComment && isset(Tokens::PHPCS_ANNOTATION_TOKENS[$tokens[$stackPtr]['code']])) {
            // Ignore PHPCS annotation comments that are on a line by themselves
            return;
        }

        $lineLength = $tokens[$stackPtr]['column'] + $tokens[$stackPtr]['length'] - 1;

        if ($this->ignoreComments && isset(Tokens::COMMENT_TOKENS[$tokens[$stackPtr]['code']])) {
            // Trailing comments are being ignored in line length calculations
            if ($onlyComment) {
                // The comment is the only thing on the line, so no need to check length
                return;
            }

            $lineLength -= $tokens[$stackPtr]['length'];
        }

        // Record metrics for common line length groupings
        if ($lineLength <= 80) {
            $phpcsFile->recordMetric($stackPtr, 'Line length', '80 or less');
        } elseif ($lineLength <= 120) {
            $phpcsFile->recordMetric($stackPtr, 'Line length', '81-120');
        } elseif ($lineLength <= 150) {
            $phpcsFile->recordMetric($stackPtr, 'Line length', '121-150');
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Line length', '151 or more');
        }

        if ($onlyComment && $lineLength > $this->lineLimit) {
            // If this is a long comment, check if it can be broken up onto multiple lines.
            // Some comments contain unbreakable strings like URLs and so it makes sense
            // to ignore the line length in these cases if the URL would be longer than the max
            // line length once you indent it to the correct level
            $oldLength = \strlen($tokens[$stackPtr]['content']);
            $newLength = \strlen(\ltrim($tokens[$stackPtr]['content'], "/#\t "));
            $indent = ($tokens[$stackPtr]['column'] - 1) + ($oldLength - $newLength);

            $nonBreakingLength = $tokens[$stackPtr]['length'];

            $space = \strrpos($tokens[$stackPtr]['content'], ' ');
            if ($space !== false) {
                $nonBreakingLength -= $space + 1;
            }

            if (($nonBreakingLength + $indent) > $this->lineLimit) {
                return;
            }
        }

        if ($lineLength > $this->lineLimit && $this->isIgnoredLine($phpcsFile, $stackPtr)) {
            return;
        }

        if ($this->absoluteLineLimit > 0 && $lineLength > $this->absoluteLineLimit) {
            $phpcsFile->addError(
                'Line exceeds maximum limit of %s characters; contains %s characters',
                $stackPtr,
                'MaxExceeded',
                [$this->absoluteLineLimit, $lineLength]
            );
        } elseif ($lineLength > $this->lineLimit) {
            $phpcsFile->addWarning(
                'Line exceeds %s characters; contains %s characters',
                $stackPtr,
                'TooLong',
                [$this->lineLimit, $lineLength]
            );
        }
    }

    /**
     * @param int $stackPtr The last token on the line
     */
    private function isIgnoredLine(File $phpcsFile, int $stackPtr): bool
    {
        $tokens = $phpcsFile->getTokens();
        if (
            $this->ignoreConstants === false
            && $this->ignoreEnums === false
            && $this->ignoreStaticMethods === false
        ) {
            return false;
        }

        $line = $tokens[$stackPtr]['line'];
        for ($ptr = $stackPtr; $ptr >= 0 && $tokens[$ptr]['line'] === $line; $ptr--) {
            $code = $tokens[$ptr]['code'];

            if ($this->ignoreConstants && $code === \T_CONST) {
                return true;
            }

            if ($this->ignoreEnums && $code === \T_ENUM_CASE) {
                return true;
            }

            if ($code !== \T_DOUBLE_COLON) {
                continue;
            }

            $memberPtr = $phpcsFile->findNext(\T_WHITESPACE, $ptr + 1, null, true);
            if ($memberPtr === false || $tokens[$memberPtr]['code'] !== \T_STRING) {
                continue;
            }

            $member = $tokens[$memberPtr]['content'];
            $afterMemberPtr = $phpcsFile->findNext(\T_WHITESPACE, $memberPtr + 1, null, true);
            if (\strtolower($member) === 'class') {
                continue;
            }

            if ($afterMemberPtr !== false && $tokens[$afterMemberPtr]['code'] === \T_OPEN_PARENTHESIS) {
                if ($this->ignoreStaticMethods) {
                    return true;
                }

                continue;
            }

            $isConstant = \preg_match('/^[A-Z][A-Z0-9_]*$/', $member) === 1;
            if ($isConstant ? $this->ignoreConstants : $this->ignoreEnums) {
                return true;
            }
        }

        return false;
    }
}
