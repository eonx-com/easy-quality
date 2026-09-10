<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Files\LineLengthSniff\Fixture\Correct;

final class UnbreakableReferences
{
    public function run(): void
    {
        $constant = SomeVeryLongClassNameForDemonstrationPurposesOnlyAndNothingElse::SOME_VERY_LONG_CONSTANT_NAME_FOR_DEMO;
        $enumCase = SomeVeryLongEnumNameForDemonstrationPurposesOnlyAndNothingElse::SomeVeryLongEnumCaseNameForDemonstration;
        $method = SomeVeryLongClassNameForDemonstrationPurposesOnlyAndNothingElse::someVeryLongStaticMethodNameForDemo();
        $self = self::SOME_VERY_LONG_CONSTANT_NAME_FOR_DEMONSTRATION_PURPOSES_ONLY_AND_NOTHING_ELSE_AT_ALL_REALLY;
        $short = 'short line';
    }
}
