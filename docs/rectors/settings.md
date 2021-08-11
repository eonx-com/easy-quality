---eonx_docs--- title: Rector settings weight: 2000 is_section: true ---eonx_docs---

### Example configuration

```php
// rector.php
declare(strict_types=1);

use EonX\EasyQuality\Rector\PhpDocReturnForIterableRector;
use EonX\EasyQuality\Rector\ReturnArrayToYieldRector;
use EonX\EasyQuality\Rector\StrictInArrayRector;
use EonX\EasyQuality\ValueObject\PhpDocReturnForIterable;
use EonX\EasyQuality\ValueObject\ReturnArrayToYield;
use EonX\EasyQuality\Sniffs\Commenting\AnnotationSortingSniff;
use PHPUnit\Framework\TestCase;
use Rector\CodeQuality\Rector\Array_\CallableThisArrayToAnonymousFunctionRector;
use Rector\CodeQuality\Rector\Array_\ArrayThisCallToThisMethodCallRector;
use Rector\CodeQuality\Rector\Catch_\ThrowWithPreviousExceptionRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Rector\SymfonyPhpConfig\inline_value_objects;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::AUTO_IMPORT_NAMES, true);

    $parameters->set(Option::IMPORT_SHORT_CLASSES, false);

    $parameters->set(Option::IMPORT_DOC_BLOCKS, false);

    $parameters->set(Option::PHP_VERSION_FEATURES, '7.4');

    $parameters->set(Option::AUTOLOAD_PATHS, [
        __DIR__ . '/phpunit/phpunit-8.5-0/src',
    ]);

    $parameters->set(Option::PATHS, [__DIR__ . '/src', __DIR__ . '/tests']);

    $parameters->set(Option::EXCLUDE_PATHS, [
            __DIR__ . '/path/to/folder/*',
    ]);

    $parameters->set(Option::SKIP, [
        CallableThisArrayToAnonymousFunctionRector::class => null,
        ArrayThisCallToThisMethodCallRector::class => [
            __DIR__ . '/path/to/file.php',
            __DIR__ . '/path/to/folder/*',
        ],
    ]);

    $services = $containerConfigurator->services();

    $services->set(StrictInArrayRector::class);
    $services->set(ThrowWithPreviousExceptionRector::class);
    $services->set(ExplicitBoolCompareRector::class);
    $services->set(AnnotationSortingSniff::class)
        ->property('alwaysTopAnnotations', [
            '@param',
            '@return',
            '@throws',
        ]);
    $services->set(ReturnArrayToYieldRector::class)
        ->call('configure', [
            [
                ReturnArrayToYieldRector::METHODS_TO_YIELDS => inline_value_objects([
                    new ReturnArrayToYield(TestCase::class, 'provide*'),
                ]),
            ],
        ]);
    $services->set(PhpDocReturnForIterableRector::class)
        ->call('configure', [
            [
                PhpDocReturnForIterableRector::METHODS_TO_UPDATE => inline_value_objects([
                    new PhpDocReturnForIterable(TestCase::class, 'provide*'),
                ]),
            ],
        ]);
};
```

### List of parameters

- `auto_import_names` - whether to automatically import fully qualified class names [default: false]
- `autoload_paths` - list of paths to autoload (Rector relies on the autoload setup of your project; Composer autoload
  is included by default)
- `exclude_paths` - list of files/directories to skip
- `exclude_rectors` - list of rectors to exclude from analysis
- `import_doc_blocks` - whether to skip classes used in PHP DocBlocks, like in `/** @var \Some\Class */` [default: true]
- `import_short_classes` - whether to import root namespace classes, like \DateTime and \Exception [default: true]
- `paths` - list of paths to analyse
- `php_version_features` - use features of a specific PHP version [default: your PHP version]
- `skip` - list of files/directories to skip per rule
