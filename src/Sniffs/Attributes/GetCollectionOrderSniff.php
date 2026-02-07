<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Attributes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * The sniff checks that ApiResource attributes have 'order' specified when GetCollection
 * operations are enabled.
 *
 * When the 'operations' parameter is omitted, API Platform enables default operations including
 * GetCollection, so 'order' must be specified at the top level.
 *
 * When the 'operations' parameter is explicitly defined with GetCollection operations,
 * 'order' must be specified either at the top level or in each GetCollection operation.
 *
 * This ensures that API Platform GetCollection endpoints have proper sorting configured,
 * preventing unpredictable result ordering.
 */
final class GetCollectionOrderSniff implements Sniff
{
    private const ERROR_MESSAGE = 'ApiResource with GetCollection operations must have "order" specified either '
        . 'at the top level or in each GetCollection operation';

    private const GET_COLLECTION_ORDER_MISSING = 'GetCollectionOrderMissing';

    private const OPERATION_GET_COLLECTION = 'GetCollection';

    private const PARAM_NAME_ORDER = 'order';

    public function process(File $phpcsFile, $stackPtr): void
    {
        if ($this->isApiResourceAttribute($phpcsFile, $stackPtr) === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        $attributeCloser = $tokens[$stackPtr]['attribute_closer'];

        $operationsPtr = $this->findParameter($phpcsFile, $stackPtr + 1, $attributeCloser, 'operations');

        // No operations means default operations including GetCollection
        if ($operationsPtr === null) {
            $this->validateNoOperationsDefined($phpcsFile, $stackPtr, $attributeCloser);

            return;
        }

        // Find operations array boundaries
        $operationsArrayStart = $phpcsFile->findNext(\T_OPEN_SHORT_ARRAY, $operationsPtr + 1, $attributeCloser);
        if ($operationsArrayStart === false) {
            return;
        }

        $operationsArrayEnd = $tokens[$operationsArrayStart]['bracket_closer'];
        if (\is_int($operationsArrayEnd) === false) {
            return;
        }

        // Top-level `order` parameter covers all operations
        if (
            $this->hasTopLevelOrder($phpcsFile, $stackPtr, $attributeCloser, $operationsArrayStart, $operationsArrayEnd)
        ) {
            return;
        }

        // Validate each GetCollection has an 'order' parameter
        if ($this->hasGetCollectionWithoutOrder($phpcsFile, $operationsArrayStart, $operationsArrayEnd)) {
            $phpcsFile->addError(
                self::ERROR_MESSAGE,
                $stackPtr,
                self::GET_COLLECTION_ORDER_MISSING
            );
        }
    }

    public function register(): array
    {
        return [\T_ATTRIBUTE];
    }

    private function findParameter(File $phpcsFile, int $startPtr, int $endPtr, string $paramName): ?int
    {
        $tokens = $phpcsFile->getTokens();

        for ($i = $startPtr; $i <= $endPtr; $i++) {
            if ($tokens[$i]['code'] === \T_PARAM_NAME && $tokens[$i]['content'] === $paramName) {
                return $i;
            }
        }

        return null;
    }

    private function getShortClassNameAfterNew(File $phpcsFile, int $newPtr, int $maxPtr): ?string
    {
        $tokens = $phpcsFile->getTokens();

        $classNamePtr = $phpcsFile->findNext(
            [\T_STRING, \T_NS_SEPARATOR],
            $newPtr + 1,
            $maxPtr,
            false,
            null,
            true // Skip nested structures
        );

        if ($classNamePtr === false) {
            return null;
        }

        // Find the last T_STRING token (the actual class name without namespace)
        $lastClassName = null;
        $currentTokenPtr = $classNamePtr;
        while ($currentTokenPtr < $maxPtr) {
            $tokenCode = $tokens[$currentTokenPtr]['code'];
            if ($tokenCode !== \T_STRING && $tokenCode !== \T_NS_SEPARATOR) {
                break;
            }

            if ($tokenCode === \T_STRING) {
                $lastClassName = $tokens[$currentTokenPtr]['content'];
            }

            $currentTokenPtr++;
        }

        return $lastClassName;
    }

    /**
     * Check if any GetCollection operation in the operations array lacks an 'order' parameter.
     */
    private function hasGetCollectionWithoutOrder(File $phpcsFile, int $arrayStart, int $arrayEnd): bool
    {
        $searchPosition = $arrayStart + 1;

        while ($searchPosition < $arrayEnd) {
            $newPtr = $phpcsFile->findNext(\T_NEW, $searchPosition, $arrayEnd);
            if ($newPtr === false) {
                break;
            }

            $className = $this->getShortClassNameAfterNew($phpcsFile, $newPtr, $arrayEnd);

            if ($className === self::OPERATION_GET_COLLECTION &&
                $this->isGetCollectionMissingOrder($phpcsFile, $newPtr, $arrayEnd)
            ) {
                return true;
            }

            $searchPosition = $newPtr + 1;
        }

        return false;
    }

    private function hasTopLevelOrder(
        File $phpcsFile,
        int $stackPtr,
        int $attributeCloser,
        int $operationsStart,
        int $operationsEnd
    ): bool {
        $orderPtr = $this->findParameter($phpcsFile, $stackPtr + 1, $attributeCloser, self::PARAM_NAME_ORDER);

        return $orderPtr !== null && ($orderPtr < $operationsStart || $orderPtr > $operationsEnd);
    }

    private function isApiResourceAttribute(File $phpcsFile, int $stackPtr): bool
    {
        $tokens = $phpcsFile->getTokens();

        $namePtr = $phpcsFile->findNext(
            [\T_STRING, \T_NS_SEPARATOR],
            $stackPtr + 1,
            $stackPtr + 10,
            false
        );

        return $namePtr !== false && \str_contains($tokens[$namePtr]['content'], 'ApiResource');
    }

    private function isGetCollectionMissingOrder(File $phpcsFile, int $newPtr, int $maxPtr): bool
    {
        $tokens = $phpcsFile->getTokens();

        // Find position after class name
        $afterClassPtr = $phpcsFile->findNext(
            [\T_STRING, \T_NS_SEPARATOR],
            $newPtr + 1,
            $maxPtr,
            false,
            null,
            true
        );

        if ($afterClassPtr === false) {
            return false;
        }

        // Skip to end of class name
        while (
            $afterClassPtr < $maxPtr
            && ($tokens[$afterClassPtr]['code'] === \T_STRING || $tokens[$afterClassPtr]['code'] === \T_NS_SEPARATOR)
        ) {
            $afterClassPtr++;
        }

        // Find next meaningful token
        $nextTokenPtr = $phpcsFile->findNext(
            [\T_WHITESPACE, \T_COMMENT, \T_DOC_COMMENT],
            $afterClassPtr,
            $maxPtr,
            true
        );

        // No parentheses = no arguments = missing order
        if ($nextTokenPtr === false || $tokens[$nextTokenPtr]['code'] !== \T_OPEN_PARENTHESIS) {
            return true;
        }

        // Check for order parameter inside parentheses
        $closeParenPtr = $tokens[$nextTokenPtr]['parenthesis_closer'];
        if (\is_int($closeParenPtr) === false) {
            return false;
        }

        return $this->findParameter($phpcsFile, $nextTokenPtr, $closeParenPtr, self::PARAM_NAME_ORDER) === null;
    }

    private function validateNoOperationsDefined(File $phpcsFile, int $stackPtr, int $attributeCloser): void
    {
        $orderPtr = $this->findParameter($phpcsFile, $stackPtr + 1, $attributeCloser, self::PARAM_NAME_ORDER);

        if ($orderPtr === null) {
            $phpcsFile->addError(
                self::ERROR_MESSAGE,
                $stackPtr,
                self::GET_COLLECTION_ORDER_MISSING
            );
        }
    }
}
