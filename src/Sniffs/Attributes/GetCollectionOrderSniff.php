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

    private const ERROR_MESSAGE_EMPTY_ORDER = 'Order parameter cannot be an empty array';

    private const ERROR_MESSAGE_INVALID_ORDER = 'Order parameter must contain "field" => "direction" pairs';

    private const GET_COLLECTION_ORDER_EMPTY = 'GetCollectionOrderEmpty';

    private const GET_COLLECTION_ORDER_INVALID = 'GetCollectionOrderInvalid';

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

        if ($orderPtr === null || ($orderPtr >= $operationsStart && $orderPtr <= $operationsEnd)) {
            return false;
        }

        // Validate the order value
        $this->validateOrderValue($phpcsFile, $orderPtr, $attributeCloser, $stackPtr);

        return true;
    }

    private function isApiResourceAttribute(File $phpcsFile, int $stackPtr): bool
    {
        $tokens = $phpcsFile->getTokens();

        return $tokens[$stackPtr + 1]['content'] === 'ApiResource';
    }

    private function isGetCollectionMissingOrder(File $phpcsFile, int $newPtr, int $maxPtr): bool
    {
        $tokens = $phpcsFile->getTokens();

        // Find opening parenthesis after 'new GetCollection'
        $openParenPtr = $phpcsFile->findNext(\T_OPEN_PARENTHESIS, $newPtr + 1, $maxPtr);

        // No parentheses = no arguments = missing order
        if ($openParenPtr === false) {
            return true;
        }

        $closeParenPtr = $tokens[$openParenPtr]['parenthesis_closer'];
        if (\is_int($closeParenPtr) === false) {
            return false;
        }

        // Check for order parameter inside parentheses
        $orderPtr = $this->findParameter($phpcsFile, $openParenPtr, $closeParenPtr, self::PARAM_NAME_ORDER);

        if ($orderPtr === null) {
            return true;
        }

        // Validate the order value
        $this->validateOrderValue($phpcsFile, $orderPtr, $closeParenPtr, $newPtr);

        return false;
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

            return;
        }

        // Validate the order value
        $this->validateOrderValue($phpcsFile, $orderPtr, $attributeCloser, $stackPtr);
    }

    /**
     * Validates that the order parameter value is not empty and contains 'field' => 'direction' pairs.
     */
    private function validateOrderValue(File $phpcsFile, int $orderPtr, int $maxPtr, int $errorPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        // Find the array after the order parameter
        $arrayStart = $phpcsFile->findNext(\T_OPEN_SHORT_ARRAY, $orderPtr + 1, $maxPtr);
        if ($arrayStart === false) {
            return;
        }

        $arrayEnd = $tokens[$arrayStart]['bracket_closer'];
        if (\is_int($arrayEnd) === false) {
            return;
        }

        // Check if array is empty
        $firstToken = $phpcsFile->findNext(
            [\T_WHITESPACE, \T_COMMENT, \T_DOC_COMMENT],
            $arrayStart + 1,
            $arrayEnd,
            true
        );

        if ($firstToken === false) {
            // Empty array
            $phpcsFile->addError(
                self::ERROR_MESSAGE_EMPTY_ORDER,
                $errorPtr,
                self::GET_COLLECTION_ORDER_EMPTY
            );

            return;
        }

        // Validate that ALL pairs are 'field' => 'direction' format
        // Count all strings in the array
        $stringCount = 0;
        $currentPtr = $arrayStart + 1;

        // Count strings
        while ($currentPtr < $arrayEnd) {
            $stringPtr = $phpcsFile->findNext(
                [\T_CONSTANT_ENCAPSED_STRING, \T_DOUBLE_QUOTED_STRING],
                $currentPtr,
                $arrayEnd
            );

            if ($stringPtr === false) {
                break;
            }

            $stringCount++;
            $currentPtr = $stringPtr + 1;
        }

        // Validate each pair has proper 'field' => 'direction' structure
        $currentPtr = $arrayStart + 1;
        $validPairCount = 0;

        while ($currentPtr < $arrayEnd) {
            $arrowPtr = $phpcsFile->findNext(\T_DOUBLE_ARROW, $currentPtr, $arrayEnd);

            if ($arrowPtr === false) {
                break;
            }

            // Check that there's a string before the arrow (field name)
            $fieldPtr = $phpcsFile->findPrevious(
                [\T_CONSTANT_ENCAPSED_STRING, \T_DOUBLE_QUOTED_STRING],
                $arrowPtr - 1,
                $arrayStart
            );

            // Check that there's a string after the arrow (direction value)
            $directionPtr = $phpcsFile->findNext(
                [\T_CONSTANT_ENCAPSED_STRING, \T_DOUBLE_QUOTED_STRING],
                $arrowPtr + 1,
                $arrayEnd
            );

            if ($fieldPtr !== false && $directionPtr !== false) {
                $validPairCount++;
            }

            $currentPtr = $arrowPtr + 1;
        }

        // All strings must be part of valid 'field' => 'direction' pairs
        // Each valid pair has 2 strings, so stringCount should equal 2 * validPairCount
        if ($stringCount === 0 || $validPairCount === 0 || $stringCount !== 2 * $validPairCount) {
            $phpcsFile->addError(
                self::ERROR_MESSAGE_INVALID_ORDER,
                $errorPtr,
                self::GET_COLLECTION_ORDER_INVALID
            );
        }
    }
}
