<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\LineLengthSniff\Fixture\Wrong;

final class LongLines
{
    public function run(): void
    {
        $string = 'some very long string, some very long string, some very long string, some very long';
        $method = SomeVeryLongClassNameForDemonstrationPurposesOnly::someVeryLongStaticMethodNameForDemo();
        $class = SomeVeryLongClassNameForDemonstrationPurposesOnly::class . SomeOtherLongClassName::class;
        $property = SomeVeryLongClassNameForDemonstrationPurposesOnly::$someVeryLongStaticPropertyNameForDemo;
    }
}
