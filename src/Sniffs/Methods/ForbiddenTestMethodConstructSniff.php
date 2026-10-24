<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Methods;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

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

    private const string EXPECT_EXCEPTION_PREFIX = 'expectexception';

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

        if ($this->isTestMethod($phpcsFile, $stackPtr) === false) {
            return;
        }

        $anonClassBodies = [];
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

            $this->processCall($phpcsFile, $pointer);
        }
    }

    /**
     * @return list<int|string>
     */
    public function register(): array
    {
        return [\T_FUNCTION];
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

    private function isTestClassLike(File $phpcsFile, int $functionPointer): bool
    {
        $conditions = $phpcsFile->getTokens()[$functionPointer]['conditions'] ?? [];
        $ownerPointer = \array_key_last($conditions);

        if ($ownerPointer === null || \in_array($conditions[$ownerPointer], [\T_CLASS, \T_TRAIT], true) === false) {
            return false;
        }

        $namespace = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $functionPointer);

        if ($namespace === null || \str_starts_with($namespace, $this->testNamespace) === false) {
            return false;
        }

        $ownerName = $phpcsFile->getDeclarationName($ownerPointer);

        if ($conditions[$ownerPointer] === \T_TRAIT) {
            return \str_contains($ownerName, 'Test');
        }

        return \str_ends_with($ownerName, 'Test') || \str_ends_with($ownerName, 'TestCase');
    }

    private function isTestMethod(File $phpcsFile, int $functionPointer): bool
    {
        if (\str_starts_with($phpcsFile->getDeclarationName($functionPointer), $this->testMethodPrefix) === false) {
            return false;
        }

        if ($phpcsFile->getMethodProperties($functionPointer)['scope'] !== 'public') {
            return false;
        }

        return $this->isTestClassLike($phpcsFile, $functionPointer);
    }

    private function processCall(File $phpcsFile, int $namePointer): void
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

        if (\str_starts_with($lowerName, self::EXPECT_EXCEPTION_PREFIX)
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

        if ($tokenCode === \T_STRING) {
            $useStatements = UseStatementHelper::getUseStatementsForPointer($phpcsFile, $namePointer);
            $import = $useStatements[UseStatement::getUniqueId(UseStatement::TYPE_FUNCTION, $name)] ?? null;

            if ($import !== null) {
                $lowerName = \strtolower($import->getFullyQualifiedTypeName());
                $name = \sprintf('%s() imported as %s', $import->getFullyQualifiedTypeName(), $name);
            }
        }

        if (\in_array($lowerName, self::SLEEP_FUNCTIONS, true)) {
            $phpcsFile->addError(\sprintf(self::MESSAGE_SLEEP_CALL, $name), $namePointer, self::CODE_SLEEP);
        }

        if (\in_array($lowerName, self::RANDOM_FUNCTIONS, true)) {
            $phpcsFile->addError(\sprintf(self::MESSAGE_RANDOM_CALL, $name), $namePointer, self::CODE_RANDOM);
        }
    }
}
