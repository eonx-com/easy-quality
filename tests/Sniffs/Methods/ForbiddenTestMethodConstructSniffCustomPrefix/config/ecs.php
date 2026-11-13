<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Methods\ForbiddenTestMethodConstructSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(ForbiddenTestMethodConstructSniff::class, [
        'testMethodPrefix' => 'it',
        'testNamespace' => 'App\Tests',
    ]);
};
