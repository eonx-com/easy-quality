<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\Attributes;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Test as PHPUnitTest;
use PHPUnit\Framework\TestCase;

final class TestAttributeTest extends TestCase
{
    #[Test]
    public function itUsesImportedAttribute(): void
    {
        $value = \time() > 0 ? 1 : 0;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function itUsesFullyQualifiedAttribute(): void
    {
        $value = \time() > 0 ? 1 : 0;
    }

    /**
     * Doc comment between attributes and modifiers.
     */
    #[DataProvider('provideValues'), Test]
    #[\Marker]
    final public static function itUsesAttributeGroups(int $value): void
    {
        $value = $value > 0 ? 1 : 0;
    }

    #[DataProvider(Test::class)]
    public function provideValues(): iterable
    {
        yield [\time() > 0 ? 1 : 0];
    }

    #[PHPUnitTest]
    public function itUsesAliasedAttribute(): void
    {
        $value = \time() > 0 ? 1 : 0;
    }

    #[Attributes\Test]
    public function itUsesNamespaceRelativeAttribute(): void
    {
        $value = \time() > 0 ? 1 : 0;
    }
}
