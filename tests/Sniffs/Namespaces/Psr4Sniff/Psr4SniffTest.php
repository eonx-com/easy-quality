<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Namespaces\Psr4Sniff;

use EonX\EasyQuality\Sniffs\Namespaces\Psr4Sniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\EasyCodingStandard\ValueObject\Configuration;

final class Psr4SniffTest extends AbstractSniffTestCase
{
    /**
     * @see testFile
     *
     * @inheritdoc
     */
    public static function provideFixtures(): iterable
    {
        yield 'Correct, PSR-4' => [
            'filePath' => __DIR__ . '/Fixture/Correct/ValidPsr4.php.inc',
        ];

        yield 'Wrong, not PSR-4' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/NotPsr4.php.inc',
            'expectedErrors' => [
                [
                    'line' => 3,
                    'code' => Psr4Sniff::class . '.PSR4Namespace',
                ],
            ],
        ];

        yield 'Wrong, single word namespace' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/SingleWordNamespace.php.inc',
            'expectedErrors' => [
                [
                    'line' => 3,
                    'code' => Psr4Sniff::class . '.PSR4Namespace',
                ],
            ],
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }

    /**
     * @param array<int, array{line: int, code: string}>|null $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testFile(string $filePath, ?array $expectedErrors = null): void
    {
        self::assertNotEmpty($this->sniffFileProcessor->getCheckers());

        $configuration = new Configuration(isFixer: false);
        $sniffFileProcessorResult = $this->sniffFileProcessor->processFile($filePath, $configuration);

        $this->checkSniffErrors($filePath, $sniffFileProcessorResult, $expectedErrors);
    }
}
