<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\ExplicitBoolCompareRector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

#[CoversClass(\EonX\EasyQuality\Rector\ExplicitBoolCompareRector::class)]
final class ExplicitBoolCompareRectorTest extends AbstractRectorTestCase
{
    /**
     * @return iterable<string, array{filePath: string}>
     *
     * @see testRule
     */
    public static function provideData(): iterable
    {
        /** @var string[] $filePath */
        foreach (self::yieldFilesFromDirectory(__DIR__ . '/Fixture') as $filePath) {
            $filePath = \strval($filePath[0]);

            yield $filePath => [
                'filePath' => $filePath,
            ];
        }
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    #[DataProvider('provideData')]
    public function testRule(string $filePath): void
    {
        $this->doTestFile($filePath);
    }
}
