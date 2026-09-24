<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Naming;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

/**
 * Detects short names for local variables, parameters and properties, equivalent to phpmd's ShortVariable.
 * A short name is reported once per its enclosing scope (function/method for variables and parameters,
 * class/trait/enum for properties), matching phpmd's per-scope deduplication.
 */
final class ShortVariableNameSniff implements Sniff
{
    public const string CODE_SHORT_VARIABLE_NAME = 'ShortVariableName';

    public bool $allowShortVariablesInLoop = true;

    /**
     * @var string[]
     */
    public array $exceptions = [];

    public int $minimum = 3;

    /**
     * @var array<string, true>
     */
    private array $reportedScopeNames = [];

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $name = \substr($tokens[$stackPtr]['content'], 1);

        if (\strlen($name) >= $this->minimum || \in_array($name, $this->exceptions, true)) {
            return;
        }

        if ($this->isSkippedContext($phpcsFile, $stackPtr)) {
            return;
        }

        $isProperty = PropertyHelper::isProperty($phpcsFile, $stackPtr, true);
        $scopePointer = $this->findScopePointer($phpcsFile, $stackPtr, $isProperty);
        $key = $phpcsFile->getFilename() . ':' . $scopePointer . ':' . $name;

        if (isset($this->reportedScopeNames[$key])) {
            return;
        }

        $this->reportedScopeNames[$key] = true;

        $phpcsFile->addError(
            \sprintf('Variable "$%s" is too short, use at least %d characters', $name, $this->minimum),
            $stackPtr,
            self::CODE_SHORT_VARIABLE_NAME,
        );
    }

    /**
     * @return list<int|string>
     */
    public function register(): array
    {
        return [\T_VARIABLE];
    }

    /**
     * @return array{0: int, 1: int}|null
     */
    private function findEnclosingParens(File $phpcsFile, int $stackPtr, int $keywordCode): ?array
    {
        $tokens = $phpcsFile->getTokens();

        foreach ($tokens[$stackPtr]['nested_parenthesis'] ?? [] as $openParenPointer => $closeParenPointer) {
            $beforePointer = TokenHelper::findPreviousEffective($phpcsFile, $openParenPointer - 1);

            if ($beforePointer !== null && $tokens[$beforePointer]['code'] === $keywordCode) {
                return [$openParenPointer, $closeParenPointer];
            }
        }

        return null;
    }

    /**
     * Conditions only cover tokens between `{` and `}`, so a non-promoted parameter (which sits in the
     * signature's parens, before the body opens) has no function condition. Fall back to the parameter
     * list's parenthesis owner, which PHPCS sets for function/closure/arrow-function signatures.
     */
    private function findScopePointer(File $phpcsFile, int $stackPtr, bool $isProperty): int
    {
        $tokens = $phpcsFile->getTokens();
        $wantedCodes = $isProperty
            ? TokenHelper::CLASS_TYPE_WITH_ANONYMOUS_CLASS_TOKEN_CODES
            : TokenHelper::FUNCTION_TOKEN_CODES;

        foreach (\array_reverse($tokens[$stackPtr]['conditions'] ?? [], true) as $conditionPointer => $conditionCode) {
            if (\in_array($conditionCode, $wantedCodes, true)) {
                return $conditionPointer;
            }
        }

        if ($isProperty) {
            return 0;
        }

        foreach ($tokens[$stackPtr]['nested_parenthesis'] ?? [] as $openParenPointer => $closeParenPointer) {
            $ownerPointer = $tokens[$openParenPointer]['parenthesis_owner'] ?? null;

            if ($ownerPointer !== null && \in_array($tokens[$ownerPointer]['code'], $wantedCodes, true)) {
                return $ownerPointer;
            }
        }

        return 0;
    }

    /**
     * @return list<string>
     */
    private function getForeachBoundNames(File $phpcsFile, int $foreachPointer): array
    {
        $tokens = $phpcsFile->getTokens();
        $openParenPointer = $tokens[$foreachPointer]['parenthesis_opener'] ?? null;
        $closeParenPointer = $tokens[$foreachPointer]['parenthesis_closer'] ?? null;

        if ($openParenPointer === null || $closeParenPointer === null) {
            return [];
        }

        $asPointer = TokenHelper::findNext($phpcsFile, \T_AS, $openParenPointer + 1, $closeParenPointer);

        if ($asPointer === null) {
            return [];
        }

        $names = [];

        for ($index = $asPointer + 1; $index < $closeParenPointer; $index++) {
            if ($tokens[$index]['code'] === \T_VARIABLE) {
                $names[] = $tokens[$index]['content'];
            }
        }

        return $names;
    }

    private function isForeachBoundVariable(File $phpcsFile, int $stackPtr): bool
    {
        $parens = $this->findEnclosingParens($phpcsFile, $stackPtr, \T_FOREACH);

        if ($parens === null) {
            return false;
        }

        [$openParenPointer, $closeParenPointer] = $parens;
        $asPointer = TokenHelper::findNext($phpcsFile, \T_AS, $openParenPointer + 1, $closeParenPointer);

        return $asPointer !== null && $stackPtr > $asPointer;
    }

    /**
     * Matches uses of a foreach key/value target inside the loop body, so the whole variable is exempt,
     * not just its binding in the `foreach (...)` header.
     */
    private function isForeachBoundVariableUsage(File $phpcsFile, int $stackPtr): bool
    {
        $tokens = $phpcsFile->getTokens();
        $name = $tokens[$stackPtr]['content'];

        return \array_any(
            \array_keys($tokens[$stackPtr]['conditions'] ?? []),
            fn(int $conditionPointer): bool => $tokens[$conditionPointer]['code'] === \T_FOREACH
                && \in_array($name, $this->getForeachBoundNames($phpcsFile, $conditionPointer), true),
        );
    }

    /**
     * Skips the caught exception variable, the whole `for (...)` header (matches phpmd's ForInit exemption,
     * widened to the full header for simplicity) and, unless disabled, the foreach key/value target variable,
     * both where it's bound and wherever it's used in the loop body.
     */
    private function isSkippedContext(File $phpcsFile, int $stackPtr): bool
    {
        if ($this->findEnclosingParens($phpcsFile, $stackPtr, \T_CATCH) !== null) {
            return true;
        }

        if ($this->findEnclosingParens($phpcsFile, $stackPtr, \T_FOR) !== null) {
            return true;
        }

        if ($this->allowShortVariablesInLoop === false) {
            return false;
        }

        return $this->isForeachBoundVariable($phpcsFile, $stackPtr)
            || $this->isForeachBoundVariableUsage($phpcsFile, $stackPtr);
    }
}
