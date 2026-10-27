<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Methods;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\NamespaceHelper;

final class ForbiddenTestMethodConstructSniff implements Sniff
{
    public const string CODE_BOOLEAN_LOGIC = 'BooleanLogic';

    public const string CODE_BRANCHING = 'Branching';

    public const string CODE_COALESCE = 'Coalesce';

    public const string CODE_EXPECT_EXCEPTION = 'ExpectException';

    public const string CODE_RANDOM = 'Random';

    public const string CODE_SLEEP = 'Sleep';

    public const string CODE_TERNARY = 'Ternary';

    public const string CODE_TRY_CATCH = 'TryCatch';

    private const array EXPECT_EXCEPTION_METHODS = [
        'expectexception',
        'expectexceptioncode',
        'expectexceptionmessage',
        'expectexceptionmessagematches',
        'expectexceptionobject',
    ];

    private const array MESSAGES = [
        self::CODE_BOOLEAN_LOGIC => 'Test method must not contain boolean logic (&&, ||, and, or, xor).'
            . ' Use one assertion for each condition.',
        self::CODE_BRANCHING => 'Test method must not contain branching (if, match, switch).'
            . ' Write one test for each path, or add a data provider with one case for each path.',
        self::CODE_COALESCE => 'Test method must not contain a null coalescing operator (??, ??=).'
            . ' Give each value explicitly in the data provider or the arrangement.',
        self::CODE_TERNARY => 'Test method must not contain a ternary operator.'
            . ' Give the expected value from the data provider.',
        self::CODE_TRY_CATCH => 'Test method must not contain try/catch/finally.'
            . ' Call the code in a closure and assert the thrown exception after the call.',
    ];

    private const string MESSAGE_EXPECT_EXCEPTION_CALL = 'Test method must not call %s().'
        . ' Call the code in a closure and assert the thrown exception after the call.';

    private const string MESSAGE_NULLSAFE = 'Test method must not contain a nullsafe operator (?->).'
        . ' Set the value explicitly, or assert that it is not null before you use it.';

    private const string MESSAGE_RANDOM_CALL = 'Test method must not call %s().'
        . ' Add a data provider with one case for each value that matters.';

    private const string MESSAGE_SLEEP_CALL = 'Test method must not call %s().'
        . ' Move the time forward with a clock, for example CarbonImmutable::setTestNow().';

    private const array NON_FUNCTION_CALL_PREDECESSORS = [
        \T_DOUBLE_COLON,
        \T_FUNCTION,
        \T_NEW,
        \T_NULLSAFE_OBJECT_OPERATOR,
        \T_OBJECT_OPERATOR,
    ];

    private const array RANDOM_FUNCTIONS = [
        'array_rand',
        'lcg_value',
        'mt_rand',
        'mt_srand',
        'rand',
        'random_int',
        'shuffle',
        'srand',
        'str_shuffle',
    ];

    private const array SELF_REFERENCES = [
        \T_PARENT,
        \T_SELF,
        \T_STATIC,
    ];

    private const array SLEEP_FUNCTIONS = [
        'sleep',
        'time_nanosleep',
        'time_sleep_until',
        'usleep',
    ];

    private const array TOKEN_CODES = [
        \T_BOOLEAN_AND => self::CODE_BOOLEAN_LOGIC,
        \T_BOOLEAN_OR => self::CODE_BOOLEAN_LOGIC,
        \T_COALESCE => self::CODE_COALESCE,
        \T_COALESCE_EQUAL => self::CODE_COALESCE,
        \T_IF => self::CODE_BRANCHING,
        \T_INLINE_THEN => self::CODE_TERNARY,
        \T_LOGICAL_AND => self::CODE_BOOLEAN_LOGIC,
        \T_LOGICAL_OR => self::CODE_BOOLEAN_LOGIC,
        \T_LOGICAL_XOR => self::CODE_BOOLEAN_LOGIC,
        \T_MATCH => self::CODE_BRANCHING,
        \T_NULLSAFE_OBJECT_OPERATOR => self::CODE_COALESCE,
        \T_SWITCH => self::CODE_BRANCHING,
        \T_TRY => self::CODE_TRY_CATCH,
    ];

    public string $testMethodPrefix = 'test';

    public string $testNamespace = 'Test';

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener'], $tokens[$stackPtr]['scope_closer']) === false) {
            return;
        }

        $ownerPointer = $this->findTestClassLikePointer($phpcsFile, $stackPtr);

        if ($ownerPointer === null || $this->isTestMethod($phpcsFile, $stackPtr, $ownerPointer) === false) {
            return;
        }

        $anonClassBodies = [];
        $functionImports = $this->getFunctionImports($phpcsFile, $ownerPointer);
        $scopeCloser = $tokens[$stackPtr]['scope_closer'];
        for ($pointer = $tokens[$stackPtr]['scope_opener'] + 1; $pointer < $scopeCloser; $pointer++) {
            if (isset($anonClassBodies[$pointer])) {
                $pointer = $anonClassBodies[$pointer];

                continue;
            }

            $tokenCode = $tokens[$pointer]['code'];

            if ($tokenCode === \T_ATTRIBUTE) {
                $pointer = $tokens[$pointer]['attribute_closer'];

                continue;
            }

            if ($tokenCode === \T_ANON_CLASS
                && isset($tokens[$pointer]['scope_opener'], $tokens[$pointer]['scope_closer'])) {
                $anonClassBodies[$tokens[$pointer]['scope_opener']] = $tokens[$pointer]['scope_closer'];
            }

            if (isset(self::TOKEN_CODES[$tokenCode])) {
                $code = self::TOKEN_CODES[$tokenCode];
                $message = $tokenCode === \T_NULLSAFE_OBJECT_OPERATOR ? self::MESSAGE_NULLSAFE : self::MESSAGES[$code];
                $phpcsFile->addError($message, $pointer, $code);
            }

            $this->processCall($phpcsFile, $pointer, $functionImports);
        }
    }

    /**
     * @return list<int|string>
     */
    public function register(): array
    {
        return [\T_FUNCTION];
    }

    private function findTestClassLikePointer(File $phpcsFile, int $functionPointer): ?int
    {
        $conditions = $phpcsFile->getTokens()[$functionPointer]['conditions'] ?? [];
        $ownerPointer = \array_key_last($conditions);

        if ($ownerPointer === null || \in_array($conditions[$ownerPointer], [\T_CLASS, \T_TRAIT], true) === false) {
            return null;
        }

        $namespace = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $functionPointer);

        if ($namespace === null || \str_starts_with($namespace, $this->testNamespace) === false) {
            return null;
        }

        $ownerName = $phpcsFile->getDeclarationName($ownerPointer);
        $isTestClassLike = $conditions[$ownerPointer] === \T_TRAIT
            ? \str_contains($ownerName, 'Test')
            : \str_ends_with($ownerName, 'Test') || \str_ends_with($ownerName, 'TestCase');

        return $isTestClassLike ? $ownerPointer : null;
    }

    /**
     * @return array<string, string>
     */
    private function getFunctionImports(File $phpcsFile, int $ownerPointer): array
    {
        $tokens = $phpcsFile->getTokens();
        $imports = [];
        // Only the imports between the namespace declaration and the class apply to the class body
        $usePointer = NamespaceHelper::findCurrentNamespacePointer($phpcsFile, $ownerPointer) ?? 0;

        while (($usePointer = $phpcsFile->findNext(\T_USE, $usePointer + 1, $ownerPointer)) !== false) {
            $keywordPointer = $phpcsFile->findNext(Tokens::EMPTY_TOKENS, $usePointer + 1, null, true);

            if ($keywordPointer === false || \strtolower($tokens[$keywordPointer]['content']) !== 'function') {
                continue;
            }

            $semicolonPointer = $phpcsFile->findNext(\T_SEMICOLON, $keywordPointer + 1);

            if ($semicolonPointer === false) {
                continue;
            }

            $imports += $this->parseFunctionImports($phpcsFile, $keywordPointer + 1, $semicolonPointer);
        }

        return $imports;
    }

    private function isSelfReferenceCall(File $phpcsFile, int $operatorPointer): bool
    {
        $tokens = $phpcsFile->getTokens();
        $receiverPointer = $phpcsFile->findPrevious(Tokens::EMPTY_TOKENS, $operatorPointer - 1, null, true);

        $operatorCode = $tokens[$operatorPointer]['code'];
        $isThis = $tokens[$receiverPointer]['content'] === '$this';

        if (\in_array($operatorCode, [\T_NULLSAFE_OBJECT_OPERATOR, \T_OBJECT_OPERATOR], true)) {
            return $isThis;
        }

        return $operatorCode === \T_DOUBLE_COLON
            && ($isThis || \in_array($tokens[$receiverPointer]['code'], self::SELF_REFERENCES, true));
    }

    private function isTestMethod(File $phpcsFile, int $functionPointer, int $ownerPointer): bool
    {
        if ($phpcsFile->getMethodProperties($functionPointer)['scope'] === 'public') {
            return \str_starts_with($phpcsFile->getDeclarationName($functionPointer), $this->testMethodPrefix);
        }

        return $phpcsFile->getTokens()[$ownerPointer]['code'] === \T_TRAIT
            || $phpcsFile->getClassProperties($ownerPointer)['is_abstract'] === false;
    }

    /**
     * @return array<string, string>
     */
    private function parseFunctionImports(File $phpcsFile, int $startPointer, int $endPointer): array
    {
        $tokens = $phpcsFile->getTokens();
        $imports = [];
        $prefix = '';
        $name = '';
        $alias = null;
        $isAlias = false;

        for ($pointer = $startPointer; $pointer <= $endPointer; $pointer++) {
            $code = $tokens[$pointer]['code'];

            if (\in_array($code, [\T_CLOSE_USE_GROUP, \T_COMMA, \T_SEMICOLON], true)) {
                if ($name !== '') {
                    $function = \ltrim($prefix . $name, '\\');
                    $alias ??= \substr((string)\strrchr('\\' . $function, '\\'), 1);
                    $imports[\strtolower($alias)] = $function;
                }

                $name = '';
                $alias = null;
                $isAlias = false;

                continue;
            }

            if ($code === \T_OPEN_USE_GROUP) {
                $prefix = $name;
                $name = '';

                continue;
            }

            if ($code === \T_AS) {
                $isAlias = true;

                continue;
            }

            if (isset(Tokens::EMPTY_TOKENS[$code])) {
                continue;
            }

            if ($isAlias) {
                $alias = $tokens[$pointer]['content'];

                continue;
            }

            $name .= $tokens[$pointer]['content'];
        }

        return $imports;
    }

    /**
     * @param array<string, string> $functionImports
     */
    private function processCall(File $phpcsFile, int $namePointer, array $functionImports): void
    {
        $tokens = $phpcsFile->getTokens();
        $tokenCode = $tokens[$namePointer]['code'];

        if ($tokenCode !== \T_STRING && $tokenCode !== \T_NAME_FULLY_QUALIFIED) {
            return;
        }

        $nextPointer = $phpcsFile->findNext(Tokens::EMPTY_TOKENS, $namePointer + 1, null, true);

        if ($tokens[$nextPointer]['code'] !== \T_OPEN_PARENTHESIS) {
            return;
        }

        $name = \ltrim($tokens[$namePointer]['content'], '\\');
        $lowerName = \strtolower($name);
        $previousPointer = $phpcsFile->findPrevious(Tokens::EMPTY_TOKENS, $namePointer - 1, null, true);

        if ($previousPointer === false) {
            return;
        }

        if (\in_array($lowerName, self::EXPECT_EXCEPTION_METHODS, true)
            && $this->isSelfReferenceCall($phpcsFile, $previousPointer)) {
            $phpcsFile->addError(
                \sprintf(self::MESSAGE_EXPECT_EXCEPTION_CALL, $name),
                $namePointer,
                self::CODE_EXPECT_EXCEPTION
            );
        }

        if (\in_array($tokens[$previousPointer]['code'], self::NON_FUNCTION_CALL_PREDECESSORS, true)) {
            return;
        }

        if ($tokenCode === \T_STRING && isset($functionImports[$lowerName])) {
            $import = $functionImports[$lowerName];
            $lowerName = \strtolower($import);
            $name = \sprintf('%s() imported as %s', $import, $name);
        }

        if (\in_array($lowerName, self::SLEEP_FUNCTIONS, true)) {
            $phpcsFile->addError(\sprintf(self::MESSAGE_SLEEP_CALL, $name), $namePointer, self::CODE_SLEEP);
        }

        if (\in_array($lowerName, self::RANDOM_FUNCTIONS, true)) {
            $phpcsFile->addError(\sprintf(self::MESSAGE_RANDOM_CALL, $name), $namePointer, self::CODE_RANDOM);
        }
    }
}
