<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Rector\SingleLineCommentRector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

#[CoversClass(\EonX\EasyQuality\Rector\SingleLineCommentRector::class)]
final class SingleLineCommentRectorTest extends AbstractRectorTestCase
{
    /**
     * @return iterable<string>
     *
     * @see testRule
     */
    public static function provideData(): iterable
    {
        return self::yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }

    #[DataProvider('provideData')]
    public function testRule(string $fileInfo): void
    {
        $this->doTestFile($fileInfo);
    }
}
