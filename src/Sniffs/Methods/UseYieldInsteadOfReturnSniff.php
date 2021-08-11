<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Methods;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ScopeHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class UseYieldInsteadOfReturnSniff implements Sniff
{
    /**
     * @var string
     */
    public const CODE_USING_YIELD_INSTEAD_RETURN = 'UsingYieldInsteadReturn';

    /**
     * @var mixed[]
     */
    public $applyTo = [];

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     *
     * @param File $phpcsFile
     * @param int $methodPointer
     */
    public function process(File $phpcsFile, $methodPointer): void
    {
        $tokens = $phpcsFile->getTokens();
        /** @var string $classFqn */
        $classFqn = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $methodPointer);
        $methodName = $tokens[$methodPointer + 2]['content'];

        $applyToPatterns = $this->getApplyToPatternsForFqn($classFqn);
        $isApplyTo = false;
        foreach ($applyToPatterns as $applyToPattern) {
            if (\preg_match($applyToPattern, $methodName)) {
                $isApplyTo = true;
            }
        }

        if ($isApplyTo && isset($tokens[$methodPointer]['scope_opener'])) {
            $firstPointerInScope = $tokens[$methodPointer]['scope_opener'] + 1;
            for ($i = $firstPointerInScope; $i < $tokens[$methodPointer]['scope_closer']; $i++) {
                if ($tokens[$i]['code'] !== T_RETURN) {
                    continue;
                }

                if (!ScopeHelper::isInSameScope($phpcsFile, $i, $firstPointerInScope)) {
                    continue;
                }

                $nextEffectiveTokenPointer = TokenHelper::findNextEffective($phpcsFile, $i + 1);
                if ($tokens[$nextEffectiveTokenPointer]['code'] !== \T_OPEN_SHORT_ARRAY) {
                    $phpcsFile->addError(
                        'Use `yield` instead `return`',
                        $nextEffectiveTokenPointer,
                        self::CODE_USING_YIELD_INSTEAD_RETURN
                    );
                }
            }
        }
    }

    /**
     * @return array<int, (int|string)>
     */
    public function register(): array
    {
        return [
            \T_FUNCTION,
        ];
    }

    /**
     * @param string $classFqn
     *
     * @return string[]
     */
    private function getApplyToPatternsForFqn(string $classFqn): array
    {
        foreach ($this->applyTo as $applyToPattern) {
            if (\preg_match($applyToPattern['namespace'], $classFqn) === 1) {
                return $applyToPattern['patterns'];
            }
        }

        return [];
    }
}
