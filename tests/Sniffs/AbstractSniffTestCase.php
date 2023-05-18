<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\Testing\Contract\ConfigAwareInterface;
use Symplify\EasyCodingStandard\ValueObject\Configuration;

abstract class AbstractSniffTestCase extends TestCase implements ConfigAwareInterface
{
    /**
     * @var string
     */
    private const SPLIT_LINE_REGEX = "#\\-\\-\\-\\-\\-\r?\n#";

    protected SniffFileProcessor $sniffFileProcessor;

    private FixerFileProcessor $fixerFileProcessor;

    protected function setUp(): void
    {
        $container = (new EasyCodingStandardKernel())->createFromConfigs([
            $this->provideConfig(),
        ]);

        /** @var \Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor $fixerFileProcessor */
        $fixerFileProcessor = $container->get(FixerFileProcessor::class);
        $this->fixerFileProcessor = $fixerFileProcessor;
        /** @var \Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor $sniffFileProcessor */
        $sniffFileProcessor = $container->get(SniffFileProcessor::class);
        $this->sniffFileProcessor = $sniffFileProcessor;
    }

    /**
     * @return iterable<array{filePath: string, errors?: array<int, array{line: int, code: string}>}>
     *
     * @see testFile
     */
    abstract public function provideFixtures(): iterable;

    /**
     * @param array<int, array{line: int, code: string}>|null $expectedErrors
     *
     * @dataProvider provideFixtures
     */
    public function testFile(string $filePath, ?array $expectedErrors = null): void
    {
        $fileContents = FileSystem::read($filePath);

        // Before and after case - we want to see a change
        if (\str_contains($fileContents, '-----')) {
            [$inputContents, $expectedContents] = Strings::split($fileContents, self::SPLIT_LINE_REGEX);
        } else {
            // No change, part before and after are the same
            $inputContents = $fileContents;
            $expectedContents = $fileContents;
        }

        $inputFilePath = \sys_get_temp_dir() . '/ecs_tests/' . \md5((string)$inputContents) . '.php';
        FileSystem::write($inputFilePath, $inputContents);

        if ($this->fixerFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->fixerFileProcessor->processFileToString($inputFilePath);
            $this->assertEquals($expectedContents, $processedFileContent);
        } elseif ($this->sniffFileProcessor->getCheckers() !== []) {
            $configuration = new Configuration(isFixer: true);
            $sniffFileProcessorResult = $this->sniffFileProcessor->processFile($inputFilePath, $configuration);

            $processedFileContent = FileSystem::read($inputFilePath);

            $this->assertEquals($expectedContents, $processedFileContent);
            $this->checkSniffErrors($inputFilePath, $sniffFileProcessorResult, $expectedErrors);
        } else {
            $this->fail('No fixers nor sniffers were found. Register them in your config.');
        }
    }

    /**
     * @param array<int, array{line: int, code: string}>|null $expectedErrors
     */
    protected function checkSniffErrors(
        string $filePath,
        array $sniffFileProcessorResult,
        ?array $expectedErrors = null,
    ): void {
        $expectedErrors ??= [];
        $expectedErrors ??= [];
        if (isset($sniffFileProcessorResult[Bridge::CODING_STANDARD_ERRORS])) {
            /** @var \Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError $error */
            foreach ($sniffFileProcessorResult[Bridge::CODING_STANDARD_ERRORS] as $errorKey => $error) {
                foreach ($expectedErrors as $expectedErrorKey => $expectedError) {
                    if (
                        $error->getLine() === $expectedError['line']
                        && $error->getCheckerClass() === $expectedError['code']
                    ) {
                        unset(
                            $expectedErrors[$expectedErrorKey],
                            $sniffFileProcessorResult[Bridge::CODING_STANDARD_ERRORS][$errorKey]
                        );

                        continue 2;
                    }
                }
            }

            if (\count($sniffFileProcessorResult[Bridge::CODING_STANDARD_ERRORS]) > 0) {
                $this->fail(\sprintf(
                    "Found errors that were not expected in file %s:\n %s",
                    $filePath,
                    \implode(
                        ', ',
                        \array_map(
                            static function (CodingStandardError $error): string {
                                return '- Line: ' . $error->getLine()
                                    . ', Error: ' . $error->getCheckerClass() . "\n";
                            },
                            $sniffFileProcessorResult[Bridge::CODING_STANDARD_ERRORS]
                        )
                    )
                ));
            }
        }

        if (\count($expectedErrors) > 0) {
            $this->fail(\sprintf(
                "Expected errors were not found in file %s:\n %s",
                $filePath,
                \implode(
                    ', ',
                    \array_map(
                        static function (array $error): string {
                            return '- Line: ' . $error['line'] . ', Error: ' . $error['code'] . "\n";
                        },
                        $expectedErrors
                    )
                )
            ));
        }
    }
}
