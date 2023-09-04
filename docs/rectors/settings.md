---eonx_docs--- title: Rector settings weight: 2000 is_section: true ---eonx_docs---

### Example configuration

```php
// rector.php
declare(strict_types=1);

use EonX\EasyQuality\Rector\PhpDocReturnForIterableRector;
use EonX\EasyQuality\Rector\ReturnArrayToYieldRector;
use EonX\EasyQuality\ValueObject\EasyQualitySetList;
use EonX\EasyQuality\ValueObject\PhpDocReturnForIterable;
use EonX\EasyQuality\ValueObject\ReturnArrayToYield;
use PHPUnit\Framework\TestCase;
use Rector\CodeQuality\Rector\Array_\ArrayThisCallToThisMethodCallRector;
use Rector\CodeQuality\Rector\Array_\CallableThisArrayToAnonymousFunctionRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        EasyQualitySetList::RECTOR,
        EasyQualitySetList::RECTOR_PHPUNIT_10,
    ]);
    $rectorConfig->autoloadPaths([__DIR__ . '/vendor']);
    $rectorConfig->importNames(true, false);
    $rectorConfig->importShortClasses();
    $rectorConfig->parallel();
    $rectorConfig->phpVersion(PhpVersion::PHP_81);
    $rectorConfig->paths([__DIR__ . '/src', __DIR__ . '/tests']);
    $rectorConfig->skip([
        __DIR__ . '/path/to/file.php',
        __DIR__ . '/path/with/mask/**/*.php',
        CallableThisArrayToAnonymousFunctionRector::class => null,
        ArrayThisCallToThisMethodCallRector::class => [
            __DIR__ . '/path/to/file.php',
            __DIR__ . '/path/to/folder/*',
        ],
    ]);

    $services = $containerConfigurator->services();

    // Add rules you want or override rules from sets
    $rectorConfig->ruleWithConfiguration(ReturnArrayToYieldRector::class, [
        ReturnArrayToYieldRector::METHODS_TO_YIELDS => [
            new ReturnArrayToYield(TestCase::class, 'provide*'),
        ],
    ]);
    $rectorConfig->ruleWithConfiguration(PhpDocReturnForIterableRector::class, [
        PhpDocReturnForIterableRector::METHODS_TO_UPDATE => [
            new PhpDocReturnForIterable(TestCase::class, 'provide*'),
        ],
    ]);
};
```
