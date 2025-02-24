<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Constants;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\StringHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

final class DisallowApplicationConstantAndEnumUsageInTestAssertBlock implements Sniff
{
    /**
     * @var string[]
     */
    private const ANONYMOUS_STRUCTURES = ['T_CLOSURE', 'T_ANON_CLASS'];

    public string $applicationNamespace = 'App';

    public string $testMethodPrefix = 'test';

    public string $testNamespace = 'Test';

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        if ($this->shouldSkip($phpcsFile, $stackPtr)) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        $openTokenPosition = TokenHelper::findNext($phpcsFile, [\T_OPEN_CURLY_BRACKET], $stackPtr);
        if ($openTokenPosition === null) {
            return;
        }

        $closeTokenPosition = $tokens[$openTokenPosition]['bracket_closer'];
        if ($closeTokenPosition === null) {
            return;
        }

        if ($this->isSingleLineMethod($phpcsFile, $openTokenPosition, $closeTokenPosition)) {
            return;
        }

        $currentTokenPosition = $openTokenPosition;
        $previousLine = $tokens[$openTokenPosition]['line'];
        $emptyLines = 0;
        $inAnonymousStructure = false;
        $bracketsLevel = 0;

        while ($currentTokenPosition < $closeTokenPosition) {
            // Find next token skipping whitespaces
            $nextTokenPosition = TokenHelper::findNextExcluding(
                $phpcsFile,
                [\T_WHITESPACE],
                $currentTokenPosition + 1
            );
            if (\in_array($tokens[$currentTokenPosition]['type'], self::ANONYMOUS_STRUCTURES, true)) {
                $inAnonymousStructure = true;
            }

            if ($inAnonymousStructure && $tokens[$currentTokenPosition]['type'] === 'T_OPEN_CURLY_BRACKET') {
                $bracketsLevel++;
            }

            $currentLine = $tokens[$nextTokenPosition]['line'];
            if ($inAnonymousStructure === false && $currentLine - $previousLine > 1) {
                $emptyLines++;
            }

            if ($emptyLines >= 2) {
                $this->checkApplicationConstantAndEnumUsage(
                    $phpcsFile,
                    $stackPtr,
                    $currentTokenPosition,
                    $closeTokenPosition
                );

                return;
            }

            $previousLine = $currentLine;
            if (
                $inAnonymousStructure &&
                $tokens[$currentTokenPosition]['type'] === 'T_CLOSE_CURLY_BRACKET'
                && --$bracketsLevel === 0
            ) {
                $inAnonymousStructure = false;
            }

            $currentTokenPosition = $nextTokenPosition;
        }
    }

    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return int[]
     */
    public function register(): array
    {
        return [\T_FUNCTION];
    }

    private function checkApplicationConstantAndEnumUsage(
        File $phpcsFile,
        int $stackPtr,
        ?int $currentTokenPosition = null,
        ?int $closeTokenPosition = null
    ): void {
        $tokens = $phpcsFile->getTokens();

        while ($currentTokenPosition !== null && $currentTokenPosition < $closeTokenPosition) {
            $nextTokenPosition = TokenHelper::findNext(
                $phpcsFile,
                [\T_DOUBLE_COLON],
                $currentTokenPosition + 1,
                $closeTokenPosition
            );
            $currentTokenPosition = $nextTokenPosition;

            if ($nextTokenPosition === null) {
                return;
            }

            $previousToken = $tokens[$nextTokenPosition - 1];
            $followingToken = $tokens[$nextTokenPosition + 1];
            $postFollowingToken = $tokens[$nextTokenPosition + 2];
            if ($previousToken['type'] !== 'T_STRING' || $followingToken['type'] !== 'T_STRING') {
                continue;
            }

            // It is a class name
            if ($followingToken['content'] === 'class') {
                continue;
            }

            // It is a static function call
            if ($postFollowingToken['content'] === '(') {
                continue;
            }

            $namespace = NamespaceHelper::resolveClassName(
                $phpcsFile,
                $previousToken['content'],
                $nextTokenPosition - 1
            );

            if (NamespaceHelper::isTypeInNamespace($namespace, $this->applicationNamespace)) {
                $method = FunctionHelper::getName($phpcsFile, $stackPtr);
                $phpcsFile->addErrorOnLine(
                    "Method [{$method}] uses application constant/enum {$previousToken['content']}::" .
                    "{$followingToken['content']} in the test assert block.",
                    $tokens[$stackPtr]['line'],
                    'ApplicationConstantOrEnumUsedInAssertBlock'
                );
            }
        }
    }

    private function isSingleLineMethod(File $phpcsFile, int $openTokenPosition, int $closeTokenPosition): bool
    {
        $semicolons = TokenHelper::findNextAll($phpcsFile, [\T_SEMICOLON], $openTokenPosition, $closeTokenPosition);

        return \count($semicolons) === 1;
    }

    private function shouldSkip(File $phpcsFile, int $stackPtr): bool
    {
        $classFqn = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $stackPtr);

        if ($classFqn === null) {
            return true;
        }

        if (StringHelper::startsWith($classFqn, $this->testNamespace) === false) {
            return true;
        }

        $functionName = FunctionHelper::getName($phpcsFile, $stackPtr);

        return StringHelper::startsWith($functionName, $this->testMethodPrefix) === false;
    }
}
