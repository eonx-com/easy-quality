<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Migrations\GrantsForCreateTableMigrationSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(GrantsForCreateTableMigrationSniff::class, [
        'grantPatterns' => [
            '/GRANT .* ON ([a-z_]+)/ui',
            '/\$this->grantPermissionsOnTable\(\'([a-z_]+)\'\);/ui',
        ],
    ]);
};
