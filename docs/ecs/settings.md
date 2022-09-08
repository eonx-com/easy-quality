---eonx_docs--- title: ECS settings weight: 1000 is_section: true ---eonx_docs---

### Example configuration

```php
// ecs.php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Classes\MakeClassAbstractSniff;
use EonX\EasyQuality\Sniffs\ControlStructures\UseYieldInsteadOfReturnSniff;
use EonX\EasyQuality\ValueObject\EasyQualitySetList;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use SlevomatCodingStandard\Sniffs\Functions\StaticClosureSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->sets([EasyQualitySetList::ECS]);
    $ecsConfig->parallel();
    $ecsConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/tests',
    ]);
    $ecsConfig->skip([
        __DIR__ . '/path/to/file.php',
        __DIR__ . '/path/with/mask/**/*.php',
        LineLengthSniff::class => null,
        StaticClosureSniff::class => [
            __DIR__ . '/path/to/file.php',
            __DIR__ . '/path/to/folder/*',
        ],
    ]);
    
    // Add rules you want or override rules from sets
    $ecsConfig->rule(AvoidPrivatePropertiesSniff::class);
    $ecsConfig->ruleWithConfiguration(LineLengthSniff::class, [
        'absoluteLineLimit' => 120,
        'ignoreComments' => false,
    ]);
    $ecsConfig->ruleWithConfiguration(UseYieldInsteadOfReturnSniff::class, [
        'applyTo' => [
            [
                'namespace' => '/^Test/',
                'patterns' => [
                    '/provide[A-Z]*/',
                ],
            ],
        ],
    ]);
    $ecsConfig->ruleWithConfiguration(TestMethodNameSniff::class, [
        'ignored' => [
            '/testWebhookSendFailsOnEachAttempt/',
            '/testOnFlushSucceeds/',
            '/testParsedWithErrorsSucceeds/',
            '/testSettersAndGetters/',
            '/testSignatureIsValid/',
            '/testVoteOnAttributeSucceeds/',
        ],
    ]);
};
```
