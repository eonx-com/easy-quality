<?php
declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\Class_\AnnotationWithValueToAttributeRector;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\Class_\CoversAnnotationWithValueToAttributeRector;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\ClassMethod\DataProviderAnnotationToAttributeRector;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\ClassMethod\DependsAnnotationWithValueToAttributeRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\YieldDataProviderRector;
use Rector\PHPUnit\PHPUnit100\Rector\Class_\StaticDataProviderClassMethodRector;
use Rector\PHPUnit\PHPUnit100\Rector\MethodCall\PropertyExistsWithoutAssertRector;
use Rector\PHPUnit\ValueObject\AnnotationWithValueToAttribute;

/**
 * @see https://github.com/rectorphp/rector-phpunit/blob/main/config/sets/phpunit100.php
 * @see https://github.com/rectorphp/rector-phpunit/blob/main/config/sets/annotations-to-attributes.php
 *
 * We don't want to convert the "codeCoverageIgnore" annotation to the "CodeCoverageIgnore" attribute.
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rules([
        CoversAnnotationWithValueToAttributeRector::class,
        DataProviderAnnotationToAttributeRector::class,
        DependsAnnotationWithValueToAttributeRector::class,
        PropertyExistsWithoutAssertRector::class,
        StaticDataProviderClassMethodRector::class,
        YieldDataProviderRector::class,
    ]);

    $rectorConfig->ruleWithConfiguration(AnnotationWithValueToAttributeRector::class, [
        new AnnotationWithValueToAttribute(
            'backupGlobals',
            'PHPUnit\\Framework\\Attributes\\BackupGlobals',
            ['enabled' => true, 'disabled' => false]
        ),
        new AnnotationWithValueToAttribute(
            'backupStaticAttributes',
            'PHPUnit\\Framework\\Attributes\\BackupStaticProperties',
            ['enabled' => true, 'disabled' => false]
        ),
        new AnnotationWithValueToAttribute('depends', 'PHPUnit\\Framework\\Attributes\\Depends'),
        new AnnotationWithValueToAttribute('group', 'PHPUnit\\Framework\\Attributes\\Group'),
        new AnnotationWithValueToAttribute(
            'preserveGlobalState',
            'PHPUnit\\Framework\\Attributes\\PreserveGlobalState',
            ['enabled' => true, 'disabled' => false]
        ),
        new AnnotationWithValueToAttribute('testDox', 'PHPUnit\\Framework\\Attributes\\TestDox'),
        new AnnotationWithValueToAttribute('testWith', 'PHPUnit\\Framework\\Attributes\\TestWith'),
        new AnnotationWithValueToAttribute('testdox', 'PHPUnit\\Framework\\Attributes\\TestDox'),
        new AnnotationWithValueToAttribute('testwith', 'PHPUnit\\Framework\\Attributes\\TestWith'),
        new AnnotationWithValueToAttribute('ticket', 'PHPUnit\\Framework\\Attributes\\Ticket'),
        new AnnotationWithValueToAttribute('uses', 'PHPUnit\\Framework\\Attributes\\UsesClass'),
    ]);

    $rectorConfig->ruleWithConfiguration(AnnotationToAttributeRector::class, [
        new AnnotationToAttribute('after', 'PHPUnit\\Framework\\Attributes\\After'),
        new AnnotationToAttribute('afterClass', 'PHPUnit\\Framework\\Attributes\\AfterClass'),
        new AnnotationToAttribute('before', 'PHPUnit\\Framework\\Attributes\\Before'),
        new AnnotationToAttribute('beforeClass', 'PHPUnit\\Framework\\Attributes\\BeforeClass'),
        new AnnotationToAttribute('coversNothing', 'PHPUnit\\Framework\\Attributes\\CoversNothing'),
        new AnnotationToAttribute(
            'doesNotPerformAssertions',
            'PHPUnit\\Framework\\Attributes\\DoesNotPerformAssertions'
        ),
        new AnnotationToAttribute('large', 'PHPUnit\\Framework\\Attributes\\Large'),
        new AnnotationToAttribute('medium', 'PHPUnit\\Framework\\Attributes\\Medium'),
        new AnnotationToAttribute('postCondition', 'PHPUnit\\Framework\\Attributes\\PreCondition'),
        new AnnotationToAttribute('preCondition', 'PHPUnit\\Framework\\Attributes\\PostCondition'),
        new AnnotationToAttribute('runInSeparateProcess', 'PHPUnit\\Framework\\Attributes\\RunInSeparateProcess'),
        new AnnotationToAttribute(
            'runTestsInSeparateProcesses',
            'PHPUnit\\Framework\\Attributes\\RunTestsInSeparateProcesses'
        ),
        new AnnotationToAttribute('small', 'PHPUnit\\Framework\\Attributes\\Small'),
        new AnnotationToAttribute('test', 'PHPUnit\\Framework\\Attributes\\Test'),
    ]);
};
