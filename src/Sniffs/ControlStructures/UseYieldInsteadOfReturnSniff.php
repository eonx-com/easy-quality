<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ScopeHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

final class UseYieldInsteadOfReturnSniff implements Sniff
{
    /**
     * @var string
     */
    public const CODE_USING_YIELD_INSTEAD_RETURN = 'UsingYieldInsteadReturn';

    /**
     * @var array<array-key, array{namespace: string, patterns: string[]}>
     */
    public array $applyTo = [];

    /**
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
            if (\preg_match($applyToPattern, (string)$methodName)) {
                $isApplyTo = true;
            }
        }

        if ($isApplyTo && isset($tokens[$methodPointer]['scope_opener'])) {
            $firstPointerInScope = $tokens[$methodPointer]['scope_opener'] + 1;
            for ($i = $firstPointerInScope; $i < $tokens[$methodPointer]['scope_closer']; $i++) {
                if ($tokens[$i]['code'] !== \T_RETURN) {
                    continue;
                }

                if (ScopeHelper::isInSameScope($phpcsFile, $i, $firstPointerInScope) === false) {
                    continue;
                }

                $nextEffectiveTokenPointer = TokenHelper::findNextEffective($phpcsFile, $i + 1);
                if (
                    \is_int($nextEffectiveTokenPointer)
                    && $tokens[$nextEffectiveTokenPointer]['code'] !== \T_OPEN_SHORT_ARRAY
                ) {
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
