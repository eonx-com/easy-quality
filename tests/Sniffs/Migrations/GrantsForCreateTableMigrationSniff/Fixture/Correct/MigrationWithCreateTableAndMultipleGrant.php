<?php
declare(strict_types=1);

namespace Migration;

final class MigrationWithCreateTableAndMultipleGrant
{
    public function migrate(): void
    {
        $this->addSql('CREATE TABLE some_table (
            id INT AUTO_INCREMENT NOT NULL, 
            column VARCHAR(255) NOT NULL, 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('GRANT SELECT, INSERT, UPDATE, DELETE ON some_table TO some_user');
        $this->addSql('GRANT SELECT, INSERT, UPDATE, DELETE ON some_table TO another_user');
    }
}
