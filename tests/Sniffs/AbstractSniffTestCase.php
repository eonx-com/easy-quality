<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use PHPUnit\Framework\Attributes\DataProvider;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\EasyCodingStandard\ValueObject\Configuration;

abstract class AbstractSniffTestCase extends AbstractCheckerTestCase
{
    private const SPLIT_LINE_REGEX = "#-----\r?\n#";

    protected FixerFileProcessor $fixerFileProcessor;

    protected SniffFileProcessor $sniffFileProcessor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixerFileProcessor = $this->make(FixerFileProcessor::class);
        $this->sniffFileProcessor = $this->make(SniffFileProcessor::class);
    }

    /**
     * @return iterable<array{filePath: string, errors?: array<int, array{line: int, code: string}>}>
     *
     * @see testFile
     */
    abstract public static function provideFixtures(): iterable;

    /**
     * @param array<int, array{line: int, code: string}>|null $expectedErrors
     */
    #[DataProvider('provideFixtures')]
    public function testFile(string $filePath, ?array $expectedErrors = null): void
    {
        $contents = Strings::split(FileSystem::read($filePath), self::SPLIT_LINE_REGEX);
        /** @var string $inputContents */
        $inputContents = $contents[0];
        /** @var string $expectedContents */
        $expectedContents = $contents[1] ?? $inputContents;

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
     * @param array{file_diffs?: \Symplify\EasyCodingStandard\ValueObject\Error\FileDiff[], coding_standard_errors?: \Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError[]} $sniffFileProcessorResult
     * @param array<int, array{line: int, code: string}>|null $expectedErrors
     */
    protected function checkSniffErrors(
        string $filePath,
        array $sniffFileProcessorResult,
        ?array $expectedErrors = null,
    ): void {
        $expectedErrors ??= [];
        if (isset($sniffFileProcessorResult[Bridge::CODING_STANDARD_ERRORS])) {
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
