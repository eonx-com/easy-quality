---eonx_docs--- title: ECS settings weight: 1000 is_section: true ---eonx_docs---

### Example configuration

```php
// ecs.php
declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniff\Files\LineLengthSniff;
use SlevomatCodingStandard\Sniff\Functions\StaticClosureSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use EonX\EasyQuality\Sniff\ValueObject\SetList as EonxSetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PATHS, [
        __DIR__ . '/app',
        __DIR__ . '/tests',
    ]);
    
    $containerConfigurator->import(SetList::PSR_12);
    $containerConfigurator->import(EonxSetList::EONX);
    $containerConfigurator->import(EonxSetList::PHP_CODESNIFFER);
    
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
    
    // To add rules you want or to override rules from sets.
    $services->set(LineLengthSniff::class)
        ->property('absoluteLineLimit', 120)
        ->property('ignoreComments', false);

};
```

### Parameters

- `paths` - list of paths to analyse
- `sets` - list of rules to use for analysis
- `skip` - list of files/directories to skip per rule
