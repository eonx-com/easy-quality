<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\AfterClass;
use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\Attributes\BackupStaticProperties;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\BeforeClass;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\PostCondition;
use PHPUnit\Framework\Attributes\PreCondition;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\Attributes\UsesClass;
use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\Class_\AnnotationWithValueToAttributeRector;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\Class_\CoversAnnotationWithValueToAttributeRector;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\ClassMethod\DataProviderAnnotationToAttributeRector;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\ClassMethod\DependsAnnotationWithValueToAttributeRector;
use Rector\PHPUnit\PHPUnit100\Rector\Class_\StaticDataProviderClassMethodRector;
use Rector\PHPUnit\ValueObject\AnnotationWithValueToAttribute;

/**
 * @see https://github.com/rectorphp/rector-phpunit/blob/main/config/sets/phpunit100.php
 * @see https://github.com/rectorphp/rector-phpunit/blob/main/config/sets/annotations-to-attributes.php
 *
 * We don't want to convert the "codeCoverageIgnore" annotation to the "CodeCoverageIgnore" attribute.
 */
return RectorConfig::configure()
    ->withRules([
        CoversAnnotationWithValueToAttributeRector::class,
        DataProviderAnnotationToAttributeRector::class,
        DependsAnnotationWithValueToAttributeRector::class,
        StaticDataProviderClassMethodRector::class,
    ])
    ->withConfiguredRule(AnnotationWithValueToAttributeRector::class, [
        new AnnotationWithValueToAttribute(
            'backupGlobals',
            BackupGlobals::class,
            ['enabled' => true, 'disabled' => false]
        ),
        new AnnotationWithValueToAttribute(
            'backupStaticAttributes',
            BackupStaticProperties::class,
            ['enabled' => true, 'disabled' => false]
        ),
        new AnnotationWithValueToAttribute('depends', Depends::class),
        new AnnotationWithValueToAttribute('group', Group::class),
        new AnnotationWithValueToAttribute(
            'preserveGlobalState',
            PreserveGlobalState::class,
            ['enabled' => true, 'disabled' => false]
        ),
        new AnnotationWithValueToAttribute('testDox', TestDox::class),
        new AnnotationWithValueToAttribute('testWith', TestWith::class),
        new AnnotationWithValueToAttribute('testdox', TestDox::class),
        new AnnotationWithValueToAttribute('testwith', TestWith::class),
        new AnnotationWithValueToAttribute('ticket', Ticket::class),
        new AnnotationWithValueToAttribute('uses', UsesClass::class),
    ])
    ->withConfiguredRule(AnnotationToAttributeRector::class, [
        new AnnotationToAttribute('after', After::class),
        new AnnotationToAttribute('afterClass', AfterClass::class),
        new AnnotationToAttribute('before', Before::class),
        new AnnotationToAttribute('beforeClass', BeforeClass::class),
        new AnnotationToAttribute('coversNothing', CoversNothing::class),
        new AnnotationToAttribute(
            'doesNotPerformAssertions',
            DoesNotPerformAssertions::class
        ),
        new AnnotationToAttribute('large', Large::class),
        new AnnotationToAttribute('medium', Medium::class),
        new AnnotationToAttribute('postCondition', PreCondition::class),
        new AnnotationToAttribute('preCondition', PostCondition::class),
        new AnnotationToAttribute('runInSeparateProcess', RunInSeparateProcess::class),
        new AnnotationToAttribute(
            'runTestsInSeparateProcesses',
            RunTestsInSeparateProcesses::class
        ),
        new AnnotationToAttribute('small', Small::class),
        new AnnotationToAttribute('test', Test::class),
    ]);
