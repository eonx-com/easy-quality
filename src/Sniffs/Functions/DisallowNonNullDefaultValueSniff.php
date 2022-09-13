<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_CLOSE_SHORT_ARRAY;
use const T_DOUBLE_COLON;
use const T_NS_SEPARATOR;
use const T_STRING;

final class DisallowNonNullDefaultValueSniff implements Sniff
{
    /**
     * @var string
     */
    public const INCORRECT_DEFAULT_VALUE = 'IncorrectDefaultValue';

    /**
     * @var string
     */
    public const MISSED_DEFAULT_VALUE = 'MissedDefaultValue';

    /**
     * @var string[]
     */
    private const READONLY_PATTERN = '/^(public\s|protected\s|private\s)?readonly\s/';

    /**
     * @var mixed[]
     */
    public const REPLACEABLE_TOKENS = [T_CLOSE_SHORT_ARRAY, T_STRING, T_DOUBLE_COLON, T_NS_SEPARATOR];

    public function process(File $phpcsFile, $functionPointer): void
    {
        $parameters = $phpcsFile->getMethodParameters($functionPointer);
        $tokens = $phpcsFile->getTokens();

        foreach ($parameters as $parameter) {
            if (isset($parameter['default']) === false && $parameter['nullable_type'] === false) {
                continue;
            }

            if (\preg_match(self::READONLY_PATTERN, $parameter['content'])) {
                continue;
            }

            $phpcsFile->fixer->beginChangeset();

            if (isset($parameter['default']) === false && $parameter['nullable_type']) {
                $fix = $phpcsFile->addFixableError(
                    'The default value should be `null`',
                    $parameter['content'],
                    self::MISSED_DEFAULT_VALUE
                );

                if ($fix === false) {
                    continue;
                }

                $phpcsFile->addErrorOnLine(
                    'The default value should be `null`',
                    $tokens[$parameter['token']]['line'],
                    self::MISSED_DEFAULT_VALUE
                );

                $phpcsFile->fixer->addContent($parameter['token'], ' = null');
            }

            if (isset($parameter['default']) && $parameter['default'] !== 'null') {
                $fix = $phpcsFile->addFixableError(
                    'The default value should be `null`',
                    $parameter['content'],
                    self::INCORRECT_DEFAULT_VALUE
                );

                if ($fix === false) {
                    continue;
                }

                $phpcsFile->addErrorOnLine(
                    'The default value should be `null`',
                    $tokens[$parameter['token']]['line'],
                    self::INCORRECT_DEFAULT_VALUE
                );

                $defaultTokenPtr = $parameter['default_token'];
                $nextPointer = TokenHelper::findNextEffective($phpcsFile, $defaultTokenPtr + 1);

                if (\in_array($tokens[$nextPointer]['code'], self::REPLACEABLE_TOKENS, true)) {
                    $phpcsFile->fixer->replaceToken((int)$nextPointer, '');

                    if ($tokens[$nextPointer]['code'] === T_DOUBLE_COLON) {
                        $phpcsFile->fixer->replaceToken((int)$nextPointer + 1, '');
                    }
                }

                if (\in_array($tokens[$nextPointer + 1]['code'], self::REPLACEABLE_TOKENS, true)) {
                    $phpcsFile->fixer->replaceToken((int)$nextPointer + 1, '');

                    if ($tokens[$nextPointer + 1]['code'] === T_DOUBLE_COLON) {
                        $phpcsFile->fixer->replaceToken((int)$nextPointer + 2, '');
                    }
                }

                $phpcsFile->fixer->replaceToken($defaultTokenPtr, 'null');

                if ($parameter['type_hint_token'] !== false && $parameter['type_hint'][0] !== '?') {
                    $phpcsFile->fixer->addContent($parameter['type_hint_token'] - 1, '?');
                }
            }

            $phpcsFile->fixer->endChangeset();
        }
    }

    /**
     * @return mixed[]
     */
    public function register(): array
    {
        return TokenHelper::$functionTokenCodes;
    }
}
