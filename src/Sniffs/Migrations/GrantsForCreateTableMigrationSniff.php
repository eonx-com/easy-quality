<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Migrations;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\StringHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

final class GrantsForCreateTableMigrationSniff implements Sniff
{
    public string $migrationMethodName = 'migrate';

    public string $migrationsNamespace = 'Migration';

    /**
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

        $createdTables = [];
        $grantPermissionsOnTables = [];

        $pointers = TokenHelper::findNextAll(
            $phpcsFile,
            [\T_CONSTANT_ENCAPSED_STRING],
            $openTokenPosition + 1,
            $closeTokenPosition
        );

        foreach ($pointers as $pointer) {
            $content = $tokens[$pointer]['content'];
            if (\preg_match('/CREATE TABLE (?:IF NOT EXISTS )?([a-z_\d]+)/ui', (string)$content, $matches)) {
                $createdTables[] = $matches[1];
            }

            if (\preg_match('/GRANT .* ON ([a-z_]+)/ui', (string)$content, $matches)) {
                $grantPermissionsOnTables[] = $matches[1];
            }
        }

        $missingGrantsOnTables = \array_diff($createdTables, $grantPermissionsOnTables);
        if ($missingGrantsOnTables !== []) {
            $phpcsFile->addError(
                \sprintf('Missing GRANT permissions on table(s): %s', \implode(', ', $missingGrantsOnTables)),
                $stackPtr,
                'MissingGrantPermissionOnTables'
            );
        }
    }

    public function register(): array
    {
        return [\T_FUNCTION];
    }

    private function shouldSkip(File $phpcsFile, int $stackPtr): bool
    {
        $classFqn = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $stackPtr);

        if ($classFqn === null) {
            return true;
        }

        if (StringHelper::startsWith($classFqn, $this->migrationsNamespace) === false) {
            return true;
        }

        return $this->migrationMethodName !== FunctionHelper::getName($phpcsFile, $stackPtr);
    }
}
