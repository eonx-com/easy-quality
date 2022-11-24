<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Methods;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\NamespaceHelper;

final class TestMethodNameSniff implements Sniff
{
    /**
     * @var array<array-key, array{namespace: string, patterns: string[]}>
     */
    public array $allowed = [
        [
            'namespace' => '/^App\\\Tests\\\Unit/',
            'patterns' => ['/test[A-Z]/'],
        ],
    ];

    /**
     * @var array<array-key, array{namespace: string, patterns: string[]}>
     */
    public array $forbidden = [
        [
            'namespace' => '/^App\\\Tests\\\Unit/',
            'patterns' => ['/(Succeed|Return|Throw)[^s]/'],
        ],
    ];

    /**
     * @var string[]
     */
    public array $ignored = [];

    public string $testMethodPrefix = 'test';

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        // Ignore method names that don't start with testMethodPrefix
        $this->ignored[] = '/^(?!' . \preg_quote($this->testMethodPrefix, '/') . ').*/';

        $tokens = $phpcsFile->getTokens();
        $methodName = $tokens[$stackPtr + 2]['content'];
        if ($this->shouldSkip($methodName)) {
            return;
        }

        /** @var string $classFqn */
        $classFqn = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $stackPtr);

        $allowedPatterns = $this->getAllowedPatternsForFqn($classFqn);
        foreach ($allowedPatterns as $allowedPattern) {
            if (\preg_match($allowedPattern, (string)$methodName) !== 1) {
                $phpcsFile->addErrorOnLine(
                    \sprintf('Method name [%s] must conform with regex [%s]', $methodName, $allowedPattern),
                    $tokens[$stackPtr]['line'],
                    'TestMethodNameSniff'
                );
            }
        }

        $forbiddenPatterns = $this->getForbiddenPatternsForFqn($classFqn);
        foreach ($forbiddenPatterns as $forbiddenPattern) {
            if (\preg_match($forbiddenPattern, (string)$methodName)) {
                $phpcsFile->addErrorOnLine(
                    \sprintf('Method name [%s] must not conform with regex [%s]', $methodName, $forbiddenPattern),
                    $tokens[$stackPtr]['line'],
                    'TestMethodNameSniff'
                );
            }
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

    /**
     * @return string[]
     */
    private function getAllowedPatternsForFqn(string $classFqn): array
    {
        foreach ($this->allowed as $allowedPattern) {
            if (\preg_match($allowedPattern['namespace'], $classFqn) === 1) {
                return $allowedPattern['patterns'];
            }
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function getForbiddenPatternsForFqn(string $classFqn): array
    {
        foreach ($this->forbidden as $forbiddenPattern) {
            if (\preg_match($forbiddenPattern['namespace'], $classFqn) === 1) {
                return $forbiddenPattern['patterns'];
            }
        }

        return [];
    }

    private function shouldSkip(string $methodName): bool
    {
        foreach ($this->ignored as $ignoredPattern) {
            if (\preg_match($ignoredPattern, $methodName) === 1) {
                return true;
            }
        }

        return false;
    }
}
