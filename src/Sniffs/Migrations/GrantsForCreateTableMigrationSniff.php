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
    /**
     * @var string[]
     */
    public array $grantPatterns = [
        '/GRANT .* ON ([a-z_]+)/ui',
    ];

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
        if ($closeTokenPosition === null) {
            return;
        }

        $createdTables = [];
        $grantPermissionsOnTables = [];

        $content = $phpcsFile->getTokensAsString($openTokenPosition, $closeTokenPosition);

        if (\preg_match_all('/CREATE TABLE (?:IF NOT EXISTS )?([a-z_\d]+)/ui', $content, $matches) === 1) {
            $createdTables = $matches[1];
        }

        foreach ($this->grantPatterns as $grantPattern) {
            if (\preg_match_all($grantPattern, $content, $matches) > 0) {
                $grantPermissionsOnTables = \array_merge($grantPermissionsOnTables, $matches[1]);
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
