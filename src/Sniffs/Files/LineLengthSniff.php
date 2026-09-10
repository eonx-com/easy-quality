<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Copy of \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff (squizlabs/php_codesniffer 4.0.4)
 * with additional `ignoreConstants`, `ignoreEnums` and `ignoreStaticMethods` options.
 */
final class LineLengthSniff implements Sniff
{
    /**
     * Extra indentation of a continuation line the unbreakable piece would be moved to.
     */
    private const int CONTINUATION_INDENT = 4;

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
     * Whether or not to ignore lines with a constant reference (`Foo::BAR`, i.e. UPPER_CASE member name)
     * that does not fit into the limit even when moved to its own line.
     */
    public bool $ignoreConstants = false;

    /**
     * Whether or not to ignore lines with an enum case reference (`Foo::Bar`, i.e. non-UPPER_CASE member name)
     * that does not fit into the limit even when moved to its own line.
     */
    public bool $ignoreEnums = false;

    /**
     * Whether or not to ignore lines with a static method call (`Foo::method()`)
     * that does not fit into the limit even when moved to its own line.
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

        $isAboveAbsoluteLimit = $this->absoluteLineLimit > 0 && $lineLength > $this->absoluteLineLimit;

        if ($lineLength > $this->lineLimit) {
            // Check the unbreakable piece against the limit this line actually violates
            $limit = $isAboveAbsoluteLimit ? $this->absoluteLineLimit : $this->lineLimit;
            if ($this->hasUnbreakableReference($phpcsFile, $stackPtr, $limit)) {
                return;
            }
        }

        if ($isAboveAbsoluteLimit) {
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
     * Checks whether the line contains an ignored `Class::member` reference
     * that would not fit into the limit even when moved to its own (continuation) line.
     *
     * @param int $stackPtr The last token on the line
     */
    private function hasUnbreakableReference(File $phpcsFile, int $stackPtr, int $limit): bool
    {
        if (
            $this->ignoreConstants === false
            && $this->ignoreEnums === false
            && $this->ignoreStaticMethods === false
        ) {
            return false;
        }

        $tokens = $phpcsFile->getTokens();
        $line = $tokens[$stackPtr]['line'];
        $lineStart = $stackPtr;
        while ($lineStart > 0 && $tokens[$lineStart - 1]['line'] === $line) {
            $lineStart--;
        }

        $firstNonWhiteSpace = $phpcsFile->findNext(\T_WHITESPACE, $lineStart, $stackPtr + 1, true);
        $indent = $firstNonWhiteSpace === false ? 0 : $tokens[$firstNonWhiteSpace]['column'] - 1;
        $indent += self::CONTINUATION_INDENT;

        for ($ptr = $lineStart; $ptr <= $stackPtr; $ptr++) {
            if ($tokens[$ptr]['code'] !== \T_DOUBLE_COLON) {
                continue;
            }

            $memberPtr = $phpcsFile->findNext(\T_WHITESPACE, $ptr + 1, null, true);
            if (
                $memberPtr === false
                || $tokens[$memberPtr]['code'] !== \T_STRING
                || $tokens[$memberPtr]['line'] !== $line
                || \strtolower($tokens[$memberPtr]['content']) === 'class'
            ) {
                continue;
            }

            $afterMemberPtr = $phpcsFile->findNext(\T_WHITESPACE, $memberPtr + 1, null, true);
            $isMethod = $afterMemberPtr !== false && $tokens[$afterMemberPtr]['code'] === \T_OPEN_PARENTHESIS;
            $isConstant = \preg_match('/^[A-Z][A-Z0-9_]*$/', $tokens[$memberPtr]['content']) === 1;

            if ($isMethod) {
                $isIgnored = $this->ignoreStaticMethods;
            } else {
                $isIgnored = $isConstant ? $this->ignoreConstants : $this->ignoreEnums;
            }

            if ($isIgnored === false) {
                continue;
            }

            // The unbreakable piece is `ClassName::member`; the class name is a single token before `::`
            $classPtr = $tokens[$ptr - 1]['code'] === \T_WHITESPACE ? $ptr : $ptr - 1;
            $pieceLength = $tokens[$memberPtr]['column'] + $tokens[$memberPtr]['length'] - $tokens[$classPtr]['column'];

            if ($indent + $pieceLength > $limit) {
                return true;
            }
        }

        return false;
    }
}
