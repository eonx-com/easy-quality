<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Runner;
use PHP_CodeSniffer\Sniffs\Sniff;
use ReflectionClass;

abstract class AbstractSniffTestCase extends \PHPUnit\Framework\TestCase
{

    /**
     * @param (string|int|bool|array<int|string, (string|int|bool|null)>)[] $sniffProperties
     * @param string[] $codesToCheck
     * @param string[] $cliArgs
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    protected static function checkFile(string $filePath, array $sniffProperties = [], array $codesToCheck = [], array $cliArgs = []): File
    {
        if (\defined('PHP_CODESNIFFER_CBF') === false) {
            \define('PHP_CODESNIFFER_CBF', false);
        }
        $codeSniffer = new Runner();
        $codeSniffer->config = new Config(\array_merge(['-s'], $cliArgs));
        $codeSniffer->init();

        if (count($sniffProperties) > 0) {
            $codeSniffer->ruleset->ruleset[self::getSniffName()]['properties'] = $sniffProperties;
        }

        $sniffClassName = static::getSniffClassName();
        /** @var Sniff $sniff */
        $sniff = new $sniffClassName();

        $codeSniffer->ruleset->sniffs = [$sniffClassName => $sniff];

        if (count($codesToCheck) > 0) {
            foreach (self::getSniffClassReflection()->getConstants() as $constantName => $constantValue) {
                if (str_starts_with($constantName, 'CODE_') === false || \in_array($constantValue, $codesToCheck, true)) {
                    continue;
                }

                $codeSniffer->ruleset->ruleset[\sprintf('%s.%s', self::getSniffName(), $constantValue)]['severity'] = 0;
            }
        }

        $codeSniffer->ruleset->populateTokenListeners();

        $file = new LocalFile($filePath, $codeSniffer->ruleset, $codeSniffer->config);
        $file->process();

        return $file;
    }

    private static function getSniffName(): string
    {
        return \preg_replace(
            [
                '/^EonX\\\\/',
                '/\\\\/',
                '/\.Sniffs/',
                '/Sniff$/',
            ],
            [
                '',
                '.',
                '',
                '',
            ],
            static::getSniffClassName()
        );
    }

    /**
     * @return class-string
     */
    protected static function getSniffClassName(): string
    {
        throw new \RuntimeException('Method "getSniffClassName" must be implemented in child class');
    }

    /**
     * @throws \ReflectionException
     */
    private static function getSniffClassReflection(): ReflectionClass
    {
        static $reflections = [];

        $className = static::getSniffClassName();

        return $reflections[$className] ?? $reflections[$className] = new ReflectionClass($className);
    }

    protected static function assertNoSniffErrorInFile(File $phpcsFile): void
    {
        $errors = $phpcsFile->getErrors();
        self::assertEmpty($errors, \sprintf('No errors expected, but %d errors found.', count($errors)));
    }

    protected static function assertSniffError(File $phpcsFile, int $line, string $code, ?string $message = null): void
    {
        $errors = $phpcsFile->getErrors();
        self::assertTrue(isset($errors[$line]), \sprintf('Expected error on line %s, but none found.', $line));

        $sniffCode = \sprintf('%s.%s', self::getSniffName(), $code);

        self::assertTrue(
            self::hasError($errors[$line], $sniffCode, $message),
            \sprintf(
                'Expected error %s%s, but none found on line %d.%sErrors found on line %d:%s%s%s',
                $sniffCode,
                $message !== null
                    ? \sprintf(' with message "%s"', $message)
                    : '',
                $line,
                \PHP_EOL . \PHP_EOL,
                $line,
                \PHP_EOL,
                self::getFormattedErrors($errors[$line]),
                \PHP_EOL
            )
        );
    }

    /**
     * @param (string|int)[][][] $errorsOnLine
     */
    private static function hasError(array $errorsOnLine, string $sniffCode, ?string $message): bool
    {
        $hasError = false;

        foreach ($errorsOnLine as $errorsOnPosition) {
            foreach ($errorsOnPosition as $error) {
                /** @var string $errorSource */
                $errorSource = $error['source'];
                /** @var string $errorMessage */
                $errorMessage = $error['message'];

                if (
                    $errorSource === $sniffCode
                    && (
                        $message === null
                        || str_contains($errorMessage, $message)
                    )
                ) {
                    $hasError = true;
                    break;
                }
            }
        }

        return $hasError;
    }

    /**
     * @param (string|int|bool)[][][] $errors
     */
    private static function getFormattedErrors(array $errors): string
    {
        return \implode(\PHP_EOL, \array_map(static function (array $errors): string {
            return \implode(\PHP_EOL, \array_map(static function (array $error): string {
                return \sprintf("\t%s: %s", $error['source'], $error['message']);
            }, $errors));
        }, $errors));
    }

    protected static function assertNoSniffError(File $phpcsFile, int $line): void
    {
        $errors = $phpcsFile->getErrors();
        self::assertFalse(
            isset($errors[$line]),
            \sprintf(
                'Expected no error on line %s, but found:%s%s%s',
                $line,
                \PHP_EOL . \PHP_EOL,
                isset($errors[$line]) ? self::getFormattedErrors($errors[$line]) : '',
                \PHP_EOL
            )
        );
    }

    protected static function assertAllFixedInFile(File $phpcsFile): void
    {
        $phpcsFile->disableCaching();
        $phpcsFile->fixer->fixFile();
        self::assertStringEqualsFile(\preg_replace('~(\\.php)$~', '.fixed\\1', $phpcsFile->getFilename()), $phpcsFile->fixer->getContents());
    }

}
