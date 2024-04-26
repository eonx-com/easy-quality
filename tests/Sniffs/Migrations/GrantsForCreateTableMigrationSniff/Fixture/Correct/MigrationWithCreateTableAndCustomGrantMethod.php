<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Migrations\GrantsForCreateTableMigrationSniff\Fixture\Correct;

final class MigrationWithCreateTableAndCustomGrantMethod
{
    public function migrate(): void
    {
        $this->addSql('CREATE TABLE some_table (
            id INT AUTO_INCREMENT NOT NULL, 
            column VARCHAR(255) NOT NULL, 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->grantPermissionsOnTable('some_table');
    }
}
