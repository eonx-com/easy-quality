<?php

declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Methods\TestMethodNameSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services
        ->set(TestMethodNameSniff::class)
        ->property('allowed', [
            [
                'namespace' => '/^EonX\\\EasyQuality\\\Tests\\\Sniffs\\\Methods\\\TestMethodNameSniff\\\Fixtures\\\(Correct|Wrong)$/',
                'patterns' => ['/test[A-Z]/', '/test.+(Succeeds|Fails|ThrowsException|DoesNothing)/'],
            ],
        ])
        ->property('forbidden', [
            [
                'namespace' => '/^EonX\\\EasyQuality\\\Tests\\\Sniffs\\\Methods\\\TestMethodNameSniff\\\Fixtures\\\(Correct|Wrong)$/',
                'patterns' => ['/(Succeed|Return|Throw)[^s]/', '/(Successful|SuccessFully)/'],
            ],
        ])
        ->property('ignored', ['/testIgnoredMethodNameSuccessful/'])
        ->property('testMethodPrefix', 'test');
};
