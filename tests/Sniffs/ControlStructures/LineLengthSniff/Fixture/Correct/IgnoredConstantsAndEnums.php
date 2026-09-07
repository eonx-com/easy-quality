<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\ControlStructures\LineLengthSniff\Fixture\Correct;

enum IgnoredConstantsAndEnums: string
{
    case SomeVeryLongEnumCaseNameForDemonstrationPurposesOnly = 'some_very_long_enum_case_name_for_demo';

    public const string SOME_VERY_LONG_CONSTANT_NAME_FOR_DEMONSTRATION = 'some_very_long_constant_value_for_demo';

    public function run(): void
    {
        $constant = SomeVeryLongClassNameForDemonstrationPurposesOnly::SOME_VERY_LONG_CONSTANT_NAME_FOR_DEMO;
        $enumCase = SomeVeryLongEnumNameForDemonstrationPurposesOnly::SomeVeryLongEnumCaseNameForDemonstration;
        $self = self::SOME_VERY_LONG_CONSTANT_NAME_FOR_DEMONSTRATION . self::SomeVeryLongEnumCaseNameForDemonstrationPurposesOnly->value;
        $method = SomeVeryLongClassNameForDemonstrationPurposesOnly::someVeryLongStaticMethodNameForDemo();
        $short = 'short line';
    }
}
