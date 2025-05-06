<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Migrations\GrantsForCreateTableMigrationSniff;

use EonX\EasyQuality\Sniffs\Migrations\GrantsForCreateTableMigrationSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class GrantsForCreateTableMigrationSniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritdoc
     */
    public static function provideFixtures(): iterable
    {
        yield 'Correct, no `CREATE TABLE` query' => [
            'filePath' => __DIR__ . '/Fixture/Correct/MigrationWithoutCreateTable.php',
        ];

        yield 'Correct, has both `CREATE TABLE` and `GRANT` queries' => [
            'filePath' => __DIR__ . '/Fixture/Correct/MigrationWithCreateTableAndGrant.php',
        ];

        yield 'Correct, has both `CREATE TABLE` and multiple `GRANT` queries' => [
            'filePath' => __DIR__ . '/Fixture/Correct/MigrationWithCreateTableAndMultipleGrant.php',
        ];

        yield 'Correct, has both `CREATE TABLE` and custom grant method' => [
            'filePath' => __DIR__ . '/Fixture/Correct/MigrationWithCreateTableAndCustomGrantMethod.php',
        ];

        yield 'Correct, has both `CREATE TABLE IF NOT EXISTS` and `GRANT` queries' => [
            'filePath' => __DIR__ . '/Fixture/Correct/MigrationWithCreateTableIfNotExistsAndGrant.php',
        ];

        yield 'Correct, multiple tables' => [
            'filePath' => __DIR__ . '/Fixture/Correct/MigrationWithMultipleTable.php',
        ];

        yield 'Wrong, has `CREATE TABLE`, but missing `GRANT` query' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/MigrationWithCreateTableWithoutGrant.php',
            'expectedErrors' => [
                [
                    'line' => 8,
                    'code' => GrantsForCreateTableMigrationSniff::class . '.MissingGrantPermissionOnTables',
                ],
            ],
        ];

        yield 'Wrong, has `CREATE TABLE IF NOT EXISTS`, but missing `GRANT` query' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/MigrationWithCreateTableIfNotExistsWithoutGrant.php',
            'expectedErrors' => [
                [
                    'line' => 8,
                    'code' => GrantsForCreateTableMigrationSniff::class . '.MissingGrantPermissionOnTables',
                ],
            ],
        ];

        yield 'Wrong, has both `CREATE TABLE` and `GRANT` queries, but for different tables' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/MigrationWithCreateTableAndGrantForAnotherTable.php',
            'expectedErrors' => [
                [
                    'line' => 8,
                    'code' => GrantsForCreateTableMigrationSniff::class . '.MissingGrantPermissionOnTables',
                ],
            ],
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
