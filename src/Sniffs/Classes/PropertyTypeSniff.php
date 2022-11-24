<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use Throwable;

final class PropertyTypeSniff extends AbstractVariableSniff
{
    /**
     * @var string
     */
    private const ERROR_INVALID_TYPE = 'InvalidType';

    /**
     * @var array<string, string>
     */
    public array $replacePairs = [];

    /**
     * @param int $stackPtr
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr): void
    {
        try {
            $propertyInfo = $phpcsFile->getMemberProperties($stackPtr);

            if ((\is_countable($propertyInfo) ? \count($propertyInfo) : 0) === 0) {
                return;
            }
        } catch (Throwable) {
            return;
        }

        $normalizedType = $this->normalizePropertyType($propertyInfo['type']);
        if (isset($this->replacePairs[$normalizedType]) === false) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            \sprintf(
                'Please avoid using "%s" property type, use "%s" instead',
                $normalizedType,
                $this->replacePairs[$normalizedType]
            ),
            $propertyInfo['type_token'],
            self::ERROR_INVALID_TYPE
        );

        if ($fix !== false) {
            $phpcsFile->fixer->beginChangeset();

            for ($i = $propertyInfo['type_token']; $i <= $propertyInfo['type_end_token']; $i++) {
                $phpcsFile->fixer->replaceToken(
                    $i,
                    $i === $propertyInfo['type_token'] ? $this->replacePairs[$normalizedType] : ''
                );
            }

            $phpcsFile->fixer->endChangeset();
        }
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariable(File $phpcsFile, $stackPtr): void
    {
        // Not needed for sniff
    }

    /**
     * @param int $stackPtr
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr): void
    {
        // Not needed for sniff
    }

    private function normalizePropertyType(string $propertyType): string
    {
        $parts = \explode('\\', $propertyType);

        return \ltrim($parts[(int)\count($parts) - 1], '?');
    }
}
