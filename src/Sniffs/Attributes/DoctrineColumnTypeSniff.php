<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Attributes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;

final class DoctrineColumnTypeSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_INVALID_COLUMN_TYPE = 'InvalidColumnType';

    /**
     * @var array<string, string>
     */
    public array $replacePairs = [];

    /**
     * @param int $stackPointer
     */
    public function process(File $phpcsFile, $stackPointer): void
    {
        if (\count($this->replacePairs) === 0) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        $stackPointerEnd = TokenHelper::findNext($phpcsFile, [\T_ATTRIBUTE_END], $stackPointer);

        $columnFound = false;
        for ($i = $stackPointer; $i <= $stackPointerEnd; $i++) {
            $currentToken = $tokens[$i];

            if ($currentToken['code'] === 265 && \str_contains($currentToken['content'], 'Column')) {
                $columnFound = true;
            }

            if ($columnFound && $currentToken['code'] === \T_PARAM_NAME && $currentToken['content'] === 'type') {
                $tokensToReplace = $this->findNextTypesOnly(
                    $phpcsFile,
                    [\T_STRING, \T_DOUBLE_COLON, \T_CONSTANT_ENCAPSED_STRING],
                    ++$i
                );

                if (\count($tokensToReplace) === 0) {
                    return;
                }

                $content = \trim(
                    TokenHelper::getContent(
                        $phpcsFile,
                        $tokensToReplace[0],
                        $tokensToReplace[\count($tokensToReplace) - 1]
                    ),
                    '"\''
                );

                if (isset($this->replacePairs[$content]) === false) {
                    return;
                }

                $fix = $phpcsFile->addFixableError(
                    \sprintf(
                        'Please avoid using "%s" doctrine column type use "%s" instead',
                        $content,
                        $this->replacePairs[$content]
                    ),
                    $stackPointer,
                    self::ERROR_INVALID_COLUMN_TYPE
                );

                if ($fix !== false) {
                    $this->addChangeset($phpcsFile, $tokensToReplace, $this->replacePairs[$content]);
                }
            }
        }
    }

    public function register(): array
    {
        return [\T_ATTRIBUTE];
    }

    /**
     * @param int[] $tokensToReplace
     */
    private function addChangeset(File $phpcsFile, array $tokensToReplace, string $replaceWith): void
    {
        $phpcsFile->fixer->beginChangeset();

        $needQuotes = \str_contains($replaceWith, '::') === false;

        if ($needQuotes === true) {
            $quote = $this->getQuote($phpcsFile, $tokensToReplace, $replaceWith);
            $replaceWith = $quote . \trim($replaceWith, '"\'') . $quote;
        }

        foreach ($tokensToReplace as $index => $tokenPos) {
            $phpcsFile->fixer->replaceToken($tokenPos, $index === 0 ? $replaceWith : '');
        }

        $phpcsFile->fixer->endChangeset();
    }

    /**
     * @param int[] $types
     *
     * @return int[]
     */
    private function findNextTypesOnly(File $phpcsFile, array $types, int $startPos): array
    {
        $foundPositions = [];
        $tokens = $phpcsFile->getTokens();
        for ($pos = $startPos; $pos <= \array_key_last($tokens); $pos++) {
            $token = $tokens[$pos];
            $isRequiredToken = \in_array($token['code'], $types, true) === true;
            if ($isRequiredToken === true) {
                $foundPositions[] = $pos;
            } elseif (\count($foundPositions) > 0) {
                break;
            }
        }

        return $foundPositions;
    }

    /**
     * @param int[] $tokensToReplace
     */
    private function getQuote(File $phpcsFile, array $tokensToReplace, string $replaceWith): string
    {
        $foundQuote = \mb_substr($replaceWith, 0, 1);
        if ($foundQuote === '' || \in_array($foundQuote, ['"', "'"], true) === false) {
            $firstReplacebleToken = $phpcsFile->getTokens()[$tokensToReplace[0]];
            if ($firstReplacebleToken['code'] === \T_CONSTANT_ENCAPSED_STRING) {
                $foundQuote = \mb_substr($firstReplacebleToken['content'], 0, 1);
                if (\in_array($foundQuote, ['"', "'"], true) === false) {
                    $foundQuote = '"';
                }
            }
        }

        return $foundQuote;
    }
}
