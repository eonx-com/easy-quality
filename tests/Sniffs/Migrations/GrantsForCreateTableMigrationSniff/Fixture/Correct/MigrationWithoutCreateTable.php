<?php
declare(strict_types=1);

namespace Migration;

final class MigrationWithoutCreateTable
{
    public function migrate(): void
    {
        $this->addSql('UPDATE some_table SET column = 1 WHERE id = 1');
    }
}
