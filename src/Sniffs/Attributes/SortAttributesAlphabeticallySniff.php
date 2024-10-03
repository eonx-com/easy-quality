<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Attributes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AttributeHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

final class SortAttributesAlphabeticallySniff implements Sniff
{
    private const CODE_INCORRECT_ORDER = 'IncorrectOrder';

    /**
     * @param int $attributeOpenerPointer
     */
    public function process(File $phpcsFile, $attributeOpenerPointer): void
    {
        if (AttributeHelper::isValidAttribute($phpcsFile, $attributeOpenerPointer) === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        $pointerBefore = TokenHelper::findPreviousNonWhitespace($phpcsFile, $attributeOpenerPointer - 1);

        if ($tokens[$pointerBefore]['code'] === \T_ATTRIBUTE_END) {
            return;
        }

        $attributesGroups = [AttributeHelper::getAttributes($phpcsFile, $attributeOpenerPointer)];

        $lastAttributeCloserPointer = $tokens[$attributeOpenerPointer]['attribute_closer'];

        do {
            $nextPointer = TokenHelper::findNextNonWhitespace($phpcsFile, $lastAttributeCloserPointer + 1);

            if ($nextPointer === null) {
                break;
            }

            if ($tokens[$nextPointer]['code'] !== \T_ATTRIBUTE) {
                break;
            }

            $attributesGroups[] = AttributeHelper::getAttributes($phpcsFile, $nextPointer);

            $lastAttributeCloserPointer = $tokens[$nextPointer]['attribute_closer'];
        } while (true);

        $actualOrder = $attributesGroups;

        \uasort($attributesGroups, static function (array $attributesGroup1, array $attributesGroup2): int {
            $content1 = $attributesGroup1[0]->getContent();
            $content2 = $attributesGroup2[0]->getContent();

            if ($content1 === null && $content2 === null) {
                return 0;
            }

            if ($content1 === null) {
                return -1;
            }

            if ($content2 === null) {
                return 1;
            }

            return \strnatcasecmp($content1, $content2);
        });

        \uasort($attributesGroups, static fn (
            array $attributesGroup1,
            array $attributesGroup2
        ): int => \strnatcasecmp((string)$attributesGroup1[0]->getName(), (string)$attributesGroup2[0]->getName()));

        $expectedOrder = $attributesGroups;

        if ($expectedOrder === $actualOrder) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            'Incorrect order of attributes.',
            $attributeOpenerPointer,
            self::CODE_INCORRECT_ORDER
        );

        if ($fix === false) {
            return;
        }

        $attributesGroupsContent = [];
        foreach ($attributesGroups as $attributesGroupNo => $attributesGroup) {
            $attributesGroupsContent[$attributesGroupNo] = TokenHelper::getContent(
                $phpcsFile,
                $attributesGroup[0]->getAttributePointer(),
                $tokens[$attributesGroup[0]->getAttributePointer()]['attribute_closer']
            );
        }

        $areOnSameLine = $tokens[$attributeOpenerPointer]['line'] === $tokens[$lastAttributeCloserPointer]['line'];

        $attributesStartPointer = $attributeOpenerPointer;
        $attributesEndPointer = $lastAttributeCloserPointer;
        $indentation = IndentationHelper::getIndentation($phpcsFile, $attributeOpenerPointer);

        $phpcsFile->fixer->beginChangeset();

        FixerHelper::removeBetweenIncluding($phpcsFile, $attributesStartPointer, $attributesEndPointer);

        $attributesGroupsCount = \count($attributesGroups);
        foreach (\array_keys($expectedOrder) as $position => $attributesGroupNo) {
            if ($areOnSameLine) {
                if ($position !== 0) {
                    $phpcsFile->fixer->addContent($attributesStartPointer, ' ');
                }

                $phpcsFile->fixer->addContent($attributesStartPointer, $attributesGroupsContent[$attributesGroupNo]);
            } else {
                if ($position !== 0) {
                    $phpcsFile->fixer->addContent($attributesStartPointer, $indentation);
                }

                $phpcsFile->fixer->addContent($attributesStartPointer, $attributesGroupsContent[$attributesGroupNo]);

                if ($position !== $attributesGroupsCount - 1) {
                    $phpcsFile->fixer->addNewline($attributesStartPointer);
                }
            }
        }

        $phpcsFile->fixer->endChangeset();
    }

    /**
     * @return array<int, (int|string)>
     */
    public function register(): array
    {
        return [\T_ATTRIBUTE];
    }
}
