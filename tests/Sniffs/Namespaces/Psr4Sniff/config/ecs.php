<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Namespaces\Psr4Sniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(Psr4Sniff::class, [
        'composerJsonPath' => __DIR__ . '/../Fixture/composer.json',
    ]);
};
