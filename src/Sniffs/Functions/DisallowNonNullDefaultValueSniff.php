<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use UnexpectedValueException;

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

    public const REPLACEABLE_TOKENS = [\T_CLOSE_SHORT_ARRAY, \T_STRING, \T_DOUBLE_COLON, \T_NS_SEPARATOR];

    public function process(File $phpcsFile, $functionPointer): void
    {
        $parameters = $phpcsFile->getMethodParameters($functionPointer);
        $tokens = $phpcsFile->getTokens();

        foreach ($parameters as $parameter) {
            if (isset($parameter['property_readonly']) && $parameter['property_readonly']) {
                continue;
            }

            if (isset($parameter['property_visibility'], $parameter['default'])) {
                continue;
            }

            if (isset($parameter['default']) === false && $parameter['nullable_type'] === false) {
                continue;
            }

            $phpcsFile->fixer->beginChangeset();

            if (isset($parameter['default']) === false && $parameter['nullable_type']) {
                $fix = $phpcsFile->addFixableError(
                    'The default value should be `null`',
                    (int)$parameter['content'],
                    self::MISSED_DEFAULT_VALUE
                );

                if ($fix === false) {
                    continue;
                }

                $phpcsFile->fixer->addContent($parameter['token'], ' = null');
            }

            if (isset($parameter['default']) && $parameter['default'] !== 'null') {
                $fix = $phpcsFile->addFixableError(
                    'The default value should be `null`',
                    (int)$parameter['content'],
                    self::INCORRECT_DEFAULT_VALUE
                );

                if ($fix === false) {
                    continue;
                }

                if (isset($parameter['default_token']) === false) {
                    continue;
                }

                $defaultTokenPtr = $parameter['default_token'];

                $nextPointer = TokenHelper::findNextEffective($phpcsFile, $defaultTokenPtr + 1);

                if ($nextPointer === null) {
                    throw new UnexpectedValueException('Next token not found.');
                }

                if (\in_array($tokens[$nextPointer]['code'], self::REPLACEABLE_TOKENS, true)) {
                    $phpcsFile->fixer->replaceToken($nextPointer, '');

                    if ($tokens[$nextPointer]['code'] === \T_DOUBLE_COLON) {
                        $phpcsFile->fixer->replaceToken($nextPointer + 1, '');
                    }
                }

                if (\in_array($tokens[$nextPointer + 1]['code'], self::REPLACEABLE_TOKENS, true)) {
                    $phpcsFile->fixer->replaceToken($nextPointer + 1, '');

                    if ($tokens[$nextPointer + 1]['code'] === \T_DOUBLE_COLON) {
                        $phpcsFile->fixer->replaceToken($nextPointer + 2, '');
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

    public function register(): array
    {
        return TokenHelper::$functionTokenCodes;
    }
}
