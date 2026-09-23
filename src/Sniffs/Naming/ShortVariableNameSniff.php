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

    private function findScopePointer(File $phpcsFile, int $stackPtr, bool $isProperty): int
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['conditions']) === false) {
            return 0;
        }

        $wantedCodes = $isProperty
            ? TokenHelper::CLASS_TYPE_WITH_ANONYMOUS_CLASS_TOKEN_CODES
            : TokenHelper::FUNCTION_TOKEN_CODES;

        foreach (\array_reverse($tokens[$stackPtr]['conditions'], true) as $conditionPointer => $conditionCode) {
            if (\in_array($conditionCode, $wantedCodes, true)) {
                return $conditionPointer;
            }
        }

        return 0;
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
     * Skips the caught exception variable, the whole `for (...)` header (matches phpmd's ForInit exemption,
     * widened to the full header for simplicity) and, unless disabled, the foreach key/value target variables.
     */
    private function isSkippedContext(File $phpcsFile, int $stackPtr): bool
    {
        if ($this->findEnclosingParens($phpcsFile, $stackPtr, \T_CATCH) !== null) {
            return true;
        }

        if ($this->findEnclosingParens($phpcsFile, $stackPtr, \T_FOR) !== null) {
            return true;
        }

        return $this->allowShortVariablesInLoop && $this->isForeachBoundVariable($phpcsFile, $stackPtr);
    }
}
