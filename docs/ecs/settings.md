---eonx_docs--- title: ECS settings weight: 1000 is_section: true ---eonx_docs---

### Example configuration

```php
// ecs.php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Classes\MakeClassAbstractSniff;
use EonX\EasyQuality\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use SlevomatCodingStandard\Sniffs\Functions\StaticClosureSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use EonX\EasyQuality\ValueObject\EasyQualitySetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PATHS, [
        __DIR__ . '/app',
        __DIR__ . '/tests',
    ]);
    
    $containerConfigurator->import(EasyQualitySetList::ECS);
    
    $parameters->set(Option::SKIP, [
        __DIR__ . '/path/to/file.php',
        __DIR__ . '/path/with/mask/**/*.php',
        LineLengthSniff::class => null,
        StaticClosureSniff::class => [
            __DIR__ . '/path/to/file.php',
            __DIR__ . '/path/to/folder/*',
        ],
    ]);
    
    $services = $containerConfigurator->services();
    
    // Add rules you want or override rules from sets
    $services->set(LineLengthSniff::class)
        ->property('absoluteLineLimit', 120)
        ->property('ignoreComments', false);
    
    $services->set(UseYieldInsteadOfReturnSniff::class)
        ->property('applyTo', [
            [
                'namespace' => '/^Test/',
                'patterns' => [
                    '/provide[A-Z]*/',
                ],
            ],
        ]);

    // Add or override some property in the rule from sets
    $services->get(TestMethodNameSniff::class)
        ->property('ignored', [
            '/testWebhookSendFailsOnEachAttempt/',
            '/testOnFlushSucceeds/',
            '/testParsedWithErrorsSucceeds/',
            '/testSettersAndGetters/',
            '/testSignatureIsValid/',
            '/testVoteOnAttributeSucceeds/',
        ]);
};
```

### Parameters

- `paths` - list of paths to analyse
- `sets` - list of rules to use for analysis
- `skip` - list of files/directories to skip per rule
