<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Files\LineLengthSniff\Fixture\Wrong;

final class LongLines
{
    public const array LONG_ARRAY = ['aaaaaaaaaaaaaaaa', 'bbbbbbbbbbbbbbbb', 'cccccccccccccccc', 'dddddddddddd'];

    public function run(): void
    {
        $string = 'some very long string, some very long string, some very long string, some very long';
        $class = SomeVeryLongClassNameForDemonstrationPurposesOnly::class . SomeOtherLongClassName::class;
        $property = SomeVeryLongClassNameForDemonstrationPurposesOnly::$someVeryLongStaticPropertyNameForDemo;
        $constant = $this->buildSomethingLong($argumentOne, $argumentTwo, $argumentThree, Foo::BAR, $argumentFour);
        $enumCase = $this->buildSomethingLong($argumentOne, $argumentTwo, $argumentThree, Foo::Bar, $argumentFour);
        $method = $this->buildSomethingLong($argumentOne, $argumentTwo, $argumentThree, Foo::bar(), $argumentFour);
    }
}
