<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Migrations\GrantsForCreateTableMigrationSniff;

use EonX\EasyQuality\Sniffs\Migrations\GrantsForCreateTableMigrationSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class GrantsForCreateTableMigrationSniffTest extends AbstractSniffTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }

    /**
     * @inheritDoc
     */
    public function provideFixtures(): iterable
    {
        yield 'Correct, no `CREATE TABLE` query' => [
            'filePath' => __DIR__ . '/Fixture/Correct/MigrationWithoutCreateTable.php',
        ];

        yield 'Correct, has both `CREATE TABLE` and `GRANT` queries' => [
            'filePath' => __DIR__ . '/Fixture/Correct/MigrationWithCreateTableAndGrant.php',
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
    }
}
