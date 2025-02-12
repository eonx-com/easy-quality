<?php
declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use EonX\EasyQuality\Sniffs\Classes\RepositorySniff;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(RepositorySniff::class, [
        'applyTo' => [
            'entityNamespace' => [
                '/^EonX\\\EasyQuality\\\Tests\\\Sniffs\\\Classes\\\RepositorySniff\\\Fixture' .
                '\\\(Wrong|Correct)\\\Entity$/'
            ],
            'repositoryNamespace' => [
                '/^EonX\\\EasyQuality\\\Tests\\\Sniffs\\\Classes\\\RepositorySniff\\\Fixture' .
                '\\\(Wrong|Correct)\\\Repository$/',
            ],
        ],

    ]);
};
