<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Classes\MakeClassAbstractSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(MakeClassAbstractSniff::class, [
        'applyTo' => [
            [
                'namespace' => '/^EonX\\\EasyQuality\\\Tests\\\Sniffs' .
                    '\\\Classes\\\MakeClassAbstractSniff\\\Fixture\\\(Correct|Wrong)$/',
                'patterns' => ['/.*TestCase$/'],
            ],
        ],
    ]);
};
