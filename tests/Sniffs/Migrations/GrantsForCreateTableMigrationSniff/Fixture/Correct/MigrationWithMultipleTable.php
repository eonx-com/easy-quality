<?php
declare(strict_types=1);

namespace Migration;

final class MigrationWithMultipleTable
{
    public function migrate(): void
    {
        $this->addSql('CREATE TABLE some_table_a (
            id INT AUTO_INCREMENT NOT NULL, 
            column VARCHAR(255) NOT NULL, 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE some_table_b (
            id INT AUTO_INCREMENT NOT NULL, 
            column VARCHAR(255) NOT NULL, 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('GRANT SELECT, INSERT, UPDATE, DELETE ON some_table_a TO some_user');

        $this->grantPermissionsOnTable('some_table_b');
    }
}
