<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Methods\TestMethodNameSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(TestMethodNameSniff::class, [
        'allowed' => [
            [
                'namespace' => '/^EonX\\\EasyQuality\\\Tests\\\Sniffs\\\Methods\\\TestMethodNameSniff\\\Fixtures\\\(Correct|Wrong)$/',
                'patterns' => ['/test[A-Z]/', '/test.+(Succeeds|Fails|ThrowsException|DoesNothing)/'],
            ],
        ],
        'forbidden' => [
            [
                'namespace' => '/^EonX\\\EasyQuality\\\Tests\\\Sniffs\\\Methods\\\TestMethodNameSniff\\\Fixtures\\\(Correct|Wrong)$/',
                'patterns' => ['/(Succeed|Return|Throw)[^s]/', '/(Successful|SuccessFully)/'],
            ],
        ],
        'ignored' => ['/testIgnoredMethodNameSuccessful/'],
        'testMethodPrefix' => 'test'
    ]);
};
