<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AttributeHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;

final class RepositorySniff implements Sniff
{
    /**
     * @var string
     */
    public const CODE_ENTITY_MANAGER_GET_REPOSITORY = 'EntityManagerGetRepository';

    /**
     * @var string
     */
    public const CODE_ORM_ENTITY_ATTRIBUTE_REPOSITORY_CLASS_EXISTS = 'OrmEntityAttributeRepositoryClassExists';

    /**
     * @var string
     */
    public const CODE_REPOSITORY_EXTENDS_NOT_ABSTRACT_REPOSITORY = 'ExtendsNotAbstractRepository';

    /**
     * @var array{entityNamespace: string[], repositoryNamespace: string[]}
     */
    public array $applyTo = [
        'entityNamespace' => [],
        'repositoryNamespace' => [],
    ];

    private string $abstractRepositoryPattern = '/^Abstract.*Repository/';

    private string $entityManagerGetRepository = 'EntityManager::getRepository';

    private string $ormEntityAttribute = 'ORM\Entity';

    private string $repositoryClassPattern = '/repositoryClass[ \t]*:/Di';

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === \T_DOUBLE_COLON) {
            $this->processStaticCall($phpcsFile, $stackPtr, $tokens);

            return;
        }

        /** @var string $classFqn */
        $classFqn = (string)NamespaceHelper::findCurrentNamespaceName($phpcsFile, $stackPtr);

        $allowedToProcess = $this->isDatabaseEntityAllowedToProcess($classFqn);
        if ($tokens[$stackPtr]['code'] === \T_ATTRIBUTE && $allowedToProcess === true) {
            $this->processAttribute($phpcsFile, $stackPtr);

            return;
        }

        $allowedToProcess = $this->isRepositoryAllowedToProcess($classFqn);
        if ($tokens[$stackPtr]['code'] === \T_CLASS && $allowedToProcess === true) {
            $this->processClass($phpcsFile, $stackPtr);
        }
    }

    public function register(): array
    {
        return [\T_ATTRIBUTE, \T_CLASS, \T_DOUBLE_COLON];
    }

    private function isDatabaseEntityAllowedToProcess(string $classFqn): bool
    {
        foreach ($this->applyTo['entityNamespace'] as $applyToPattern) {
            if (\preg_match($applyToPattern, $classFqn) === 1) {
                return true;
            }
        }

        return false;
    }

    private function isRepositoryAllowedToProcess(string $classFqn): bool
    {
        foreach ($this->applyTo['repositoryNamespace'] as $applyToPattern) {
            if (\preg_match($applyToPattern, $classFqn) === 1) {
                return true;
            }
        }

        return false;
    }

    private function processAttribute(File $phpcsFile, int $stackPtr): void
    {
        if (AttributeHelper::isValidAttribute($phpcsFile, $stackPtr) === false) {
            return;
        }

        $attributes = AttributeHelper::getAttributes($phpcsFile, $stackPtr);
        if ($attributes[0]->getName() !== $this->ormEntityAttribute) {
            return;
        }

        if (\preg_match($this->repositoryClassPattern, (string)$attributes[0]->getContent()) === 1) {
            $phpcsFile->addError(
                'ORM\Entity "RepositoryClass" param is not allowed',
                $stackPtr,
                self::CODE_ORM_ENTITY_ATTRIBUTE_REPOSITORY_CLASS_EXISTS
            );
        }
    }

    private function processClass(File $phpcsFile, int $stackPtr): void
    {
        $className = $phpcsFile->findExtendedClassName($stackPtr);
        if ($className !== false && \preg_match($this->abstractRepositoryPattern, $className) !== 1) {
            $phpcsFile->addError(
                'Repository should extends "AbstractRepository"',
                $stackPtr,
                self::CODE_REPOSITORY_EXTENDS_NOT_ABSTRACT_REPOSITORY
            );
        }
    }

    private function processStaticCall(File $phpcsFile, int $stackPtr, array $tokens): void
    {
        $result = $phpcsFile->getTokensAsString($stackPtr - 1, 3);
        if ($result === $this->entityManagerGetRepository) {
            $phpcsFile->addError(
                'Static call EntityManager::getRepository() is not allowed',
                $stackPtr,
                self::CODE_ENTITY_MANAGER_GET_REPOSITORY
            );
        }
    }
}
