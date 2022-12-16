<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\Annotation\Annotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff as SlevomatDocCommentSpacingSniff;

/**
 * @deprecated Use SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff instead
 */
final class DocCommentSpacingSniff extends SlevomatDocCommentSpacingSniff
{
    /**
     * @var string[][]|null
     */
    private ?array $normalizedAnnotationsGroups = null;

    /**
     * @param int $docCommentOpenerPointer
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function process(File $phpcsFile, $docCommentOpenerPointer): void
    {
        if (DocCommentHelper::isInline($phpcsFile, $docCommentOpenerPointer)) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        $firstContentStartPointer = TokenHelper::findNextExcluding(
            $phpcsFile,
            [\T_DOC_COMMENT_WHITESPACE, \T_DOC_COMMENT_STAR],
            $docCommentOpenerPointer + 1,
            $tokens[$docCommentOpenerPointer]['comment_closer']
        );

        if ($firstContentStartPointer === null) {
            return;
        }

        $firstContentEndPointer = $firstContentStartPointer;
        $actualPointer = $firstContentStartPointer;
        do {
            /** @var int $actualPointer */
            $actualPointer = TokenHelper::findNextExcluding(
                $phpcsFile,
                [\T_DOC_COMMENT_STAR, \T_DOC_COMMENT_WHITESPACE],
                $actualPointer + 1,
                $tokens[$docCommentOpenerPointer]['comment_closer'] + 1
            );

            if ($tokens[$actualPointer]['code'] !== \T_DOC_COMMENT_STRING) {
                break;
            }

            $firstContentEndPointer = $actualPointer;
        } while (true);

        $annotations = \array_merge(
            [],
            ...\array_values(AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenerPointer))
        );
        \uasort(
            $annotations,
            static fn (Annotation $a, Annotation $b): int => $a->getStartPointer() <=> $b->getEndPointer()
        );
        $annotations = \array_values($annotations);
        $annotationsCount = \count($annotations);

        $firstAnnotation = $annotationsCount > 0 ? $annotations[0] : null;

        $lastContentEndPointer = $annotationsCount > 0
            ? $annotations[$annotationsCount - 1]->getEndPointer()
            : $firstContentEndPointer;

        $this->checkLinesBeforeFirstContent($phpcsFile, $docCommentOpenerPointer, $firstContentStartPointer);
        $this->checkLinesBetweenDescriptionAndFirstAnnotation(
            $phpcsFile,
            $docCommentOpenerPointer,
            $firstContentStartPointer,
            $firstContentEndPointer,
            $firstAnnotation
        );

        if (\count($annotations) > 1) {
            if (\count($this->getAnnotationsGroups()) === 0) {
                $this->checkLinesBetweenDifferentAnnotationsTypes($phpcsFile, $docCommentOpenerPointer, $annotations);
            } else {
                $this->checkAnnotationsGroups($phpcsFile, $docCommentOpenerPointer, $annotations);
            }
        }

        $this->checkLinesAfterLastContent(
            $phpcsFile,
            $docCommentOpenerPointer,
            $tokens[$docCommentOpenerPointer]['comment_closer'],
            $lastContentEndPointer
        );
    }

    /**
     * @param \SlevomatCodingStandard\Helpers\Annotation\Annotation[] $annotations
     */
    private function checkAnnotationsGroups(File $phpcsFile, int $docCommentOpenerPointer, array $annotations): void
    {
        $tokens = $phpcsFile->getTokens();

        $annotationsGroups = [];
        $annotationsGroup = [];
        $previousAnnotation = null;
        foreach ($annotations as $annotation) {
            if (
                $previousAnnotation === null
                || (
                    $tokens[$previousAnnotation->getEndPointer()]['line'] + 1
                    === $tokens[$annotation->getStartPointer()]['line']
                )
            ) {
                $annotationsGroup[] = $annotation;
                $previousAnnotation = $annotation;

                continue;
            }

            $annotationsGroups[] = $annotationsGroup;
            $annotationsGroup = [$annotation];
            $previousAnnotation = $annotation;
        }

        if (\count($annotationsGroup) > 0) {
            $annotationsGroups[] = $annotationsGroup;
        }

        $this->checkAnnotationsGroupsOrder($phpcsFile, $docCommentOpenerPointer, $annotationsGroups, $annotations);
        $this->checkLinesBetweenAnnotationsGroups($phpcsFile, $docCommentOpenerPointer, $annotationsGroups);
    }

    /**
     * @param \SlevomatCodingStandard\Helpers\Annotation\Annotation[][] $annotationsGroups
     * @param \SlevomatCodingStandard\Helpers\Annotation\Annotation[] $annotations
     */
    private function checkAnnotationsGroupsOrder(
        File $phpcsFile,
        int $docCommentOpenerPointer,
        array $annotationsGroups,
        array $annotations
    ): void {
        $equals = static function (array $firstAnnotationsGroup, array $secondAnnotationsGroup): bool {
            $getAnnotationsPointers = static fn (Annotation $annotation): int => $annotation->getStartPointer();

            $firstAnnotationsPointers = \array_map($getAnnotationsPointers, $firstAnnotationsGroup);
            $secondAnnotationsPointers = \array_map($getAnnotationsPointers, $secondAnnotationsGroup);

            return \count(\array_diff($firstAnnotationsPointers, $secondAnnotationsPointers)) === 0
                && \count(\array_diff($secondAnnotationsPointers, $firstAnnotationsPointers)) === 0;
        };

        $sortedAnnotationsGroups = $this->sortAnnotationsToGroups($annotations);
        $incorrectAnnotationsGroupsExist = false;
        $annotationsGroupsPositions = [];

        $fix = false;
        $undefinedAnnotationsGroups = [];
        foreach ($annotationsGroups as $annotationsGroupPosition => $annotationsGroup) {
            foreach ($sortedAnnotationsGroups as $sortedAnnotationsGroupPosition => $sortedAnnotationsGroup) {
                if ($equals($annotationsGroup, $sortedAnnotationsGroup)) {
                    $annotationsGroupsPositions[$annotationsGroupPosition] = $sortedAnnotationsGroupPosition;

                    continue 2;
                }

                $undefinedAnnotationsGroup = true;
                foreach ($annotationsGroup as $annotation) {
                    foreach ($this->getAnnotationsGroups() as $annotationNames) {
                        foreach ($annotationNames as $annotationName) {
                            if ($this->isAnnotationMatched($annotation, $annotationName)) {
                                $undefinedAnnotationsGroup = false;

                                break 3;
                            }
                        }
                    }
                }

                if ($undefinedAnnotationsGroup) {
                    $undefinedAnnotationsGroups[] = $annotationsGroupPosition;

                    continue 2;
                }
            }

            $incorrectAnnotationsGroupsExist = true;

            $fix = $phpcsFile->addFixableError(
                'Incorrect annotations group.',
                $annotationsGroup[0]->getStartPointer(),
                self::CODE_INCORRECT_ANNOTATIONS_GROUP
            );
        }

        if (\count($annotationsGroupsPositions) === 0 && \count($undefinedAnnotationsGroups) > 1) {
            $incorrectAnnotationsGroupsExist = true;

            $fix = $phpcsFile->addFixableError(
                'Incorrect annotations group.',
                $annotationsGroups[0][0]->getStartPointer(),
                self::CODE_INCORRECT_ANNOTATIONS_GROUP
            );
        }

        if ($incorrectAnnotationsGroupsExist === false) {
            foreach ($undefinedAnnotationsGroups as $undefinedPosition) {
                $annotationsGroupsPositions[$undefinedPosition] = \count($annotationsGroupsPositions) > 0
                    ? \max($annotationsGroupsPositions) + 1
                    : 1;
            }

            \ksort($annotationsGroupsPositions);

            $positionsMappedToGroups = \array_keys($annotationsGroupsPositions);
            $tmp = \array_values($annotationsGroupsPositions);
            \asort($tmp);
            /** @var int[] $normalizedAnnotationsGroupsPositions */
            $normalizedAnnotationsGroupsPositions = \array_combine(
                \array_keys($positionsMappedToGroups),
                \array_keys($tmp)
            );

            foreach ($normalizedAnnotationsGroupsPositions as $normalizedPosition => $sortedPosition) {
                if ($normalizedPosition === $sortedPosition) {
                    continue;
                }

                $fix = $phpcsFile->addFixableError(
                    'Incorrect order of annotations groups.',
                    $annotationsGroups[$positionsMappedToGroups[$normalizedPosition]][0]->getStartPointer(),
                    self::CODE_INCORRECT_ORDER_OF_ANNOTATIONS_GROUPS
                );

                break;
            }
        }

        foreach ($annotationsGroups as $annotationsGroupPosition => $annotationsGroup) {
            if (\array_key_exists($annotationsGroupPosition, $annotationsGroupsPositions) === false) {
                continue;
            }

            if (
                \array_key_exists($annotationsGroupsPositions[$annotationsGroupPosition], $sortedAnnotationsGroups)
                === false
            ) {
                continue;
            }

            $sortedAnnotationsGroup = $sortedAnnotationsGroups[$annotationsGroupsPositions[$annotationsGroupPosition]];

            foreach ($annotationsGroup as $annotationPosition => $annotation) {
                if ($annotation === $sortedAnnotationsGroup[$annotationPosition]) {
                    continue;
                }

                $fix = $phpcsFile->addFixableError(
                    'Incorrent order of annotations in group.',
                    $annotation->getStartPointer(),
                    self::CODE_INCORRECT_ORDER_OF_ANNOTATIONS_IN_GROUP
                );

                break;
            }
        }

        if ($fix === false) {
            return;
        }

        $firstAnnotation = $annotationsGroups[0][0];
        $lastAnnotationsGroup = $annotationsGroups[(int)\count($annotationsGroups) - 1];
        $lastAnnotation = $lastAnnotationsGroup[(int)\count($lastAnnotationsGroup) - 1];

        $indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenerPointer);

        $firstGroup = true;
        $fixedAnnotations = '';
        foreach ($sortedAnnotationsGroups as $sortedAnnotationsGroup) {
            if ($firstGroup) {
                $firstGroup = false;
            } else {
                $fixedAnnotations = \str_repeat(
                    \sprintf('%s *%s', $indentation, $phpcsFile->eolChar),
                    SniffSettingsHelper::normalizeInteger($this->linesCountBetweenAnnotationsGroups)
                );
            }

            foreach ($sortedAnnotationsGroup as $sortedAnnotation) {
                $fixedAnnotations .= \sprintf(
                    '%s * %s%s',
                    $indentation,
                    TokenHelper::getContent(
                        $phpcsFile,
                        $sortedAnnotation->getStartPointer(),
                        $sortedAnnotation->getEndPointer()
                    ),
                    $phpcsFile->eolChar
                );
            }
        }

        $endOfLineBeforeFirstAnnotation = TokenHelper::findPreviousContent(
            $phpcsFile,
            \T_DOC_COMMENT_WHITESPACE,
            $phpcsFile->eolChar,
            $firstAnnotation->getStartPointer() - 1,
            $docCommentOpenerPointer
        );
        $endOfLineAfterLastAnnotation = TokenHelper::findNextContent(
            $phpcsFile,
            \T_DOC_COMMENT_WHITESPACE,
            $phpcsFile->eolChar,
            $lastAnnotation->getEndPointer() + 1
        );

        $phpcsFile->fixer->beginChangeset();
        if ($endOfLineBeforeFirstAnnotation === null) {
            $phpcsFile->fixer->replaceToken($docCommentOpenerPointer, '/**' . $phpcsFile->eolChar . $fixedAnnotations);
            for ($i = $docCommentOpenerPointer + 1; $i <= $endOfLineAfterLastAnnotation; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }
        } else {
            $phpcsFile->fixer->replaceToken($endOfLineBeforeFirstAnnotation + 1, $fixedAnnotations);
            for ($i = $endOfLineBeforeFirstAnnotation + 2; $i <= $endOfLineAfterLastAnnotation; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }
        }
        $phpcsFile->fixer->endChangeset();
    }

    private function checkLinesAfterLastContent(
        File $phpcsFile,
        int $docCommentOpenerPointer,
        int $docCommentCloserPointer,
        int $lastContentEndPointer
    ): void {
        $whitespaceAfterLastContent = TokenHelper::getContent(
            $phpcsFile,
            $lastContentEndPointer + 1,
            $docCommentCloserPointer
        );

        $requiredLinesCountAfterLastContent = SniffSettingsHelper::normalizeInteger($this->linesCountAfterLastContent);
        $linesCountAfterLastContent = \max(
            \substr_count($whitespaceAfterLastContent, (string)$phpcsFile->eolChar) - 1,
            0
        );
        if ($linesCountAfterLastContent === $requiredLinesCountAfterLastContent) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            \sprintf(
                'Expected %d line%s after last content, found %d.',
                $requiredLinesCountAfterLastContent,
                $requiredLinesCountAfterLastContent === 1 ? '' : 's',
                $linesCountAfterLastContent
            ),
            $lastContentEndPointer,
            self::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTENT
        );

        if ($fix === false) {
            return;
        }

        $indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenerPointer);

        $phpcsFile->fixer->beginChangeset();

        $phpcsFile->fixer->addNewline($lastContentEndPointer);
        for ($i = $lastContentEndPointer + 1; $i < $docCommentCloserPointer; $i++) {
            $phpcsFile->fixer->replaceToken($i, '');
        }

        for ($i = 1; $i <= $requiredLinesCountAfterLastContent; $i++) {
            $phpcsFile->fixer->addContent(
                $lastContentEndPointer,
                \sprintf('%s *%s', $indentation, $phpcsFile->eolChar)
            );
        }

        $phpcsFile->fixer->addContentBefore($docCommentCloserPointer, $indentation . ' ');

        $phpcsFile->fixer->endChangeset();
    }

    private function checkLinesBeforeFirstContent(
        File $phpcsFile,
        int $docCommentOpenerPointer,
        int $firstContentStartPointer
    ): void {
        $tokens = $phpcsFile->getTokens();

        $whitespaceBeforeFirstContent = \substr(
            (string)$tokens[$docCommentOpenerPointer]['content'],
            0,
            \strlen('/**')
        );
        $whitespaceBeforeFirstContent .= TokenHelper::getContent(
            $phpcsFile,
            $docCommentOpenerPointer + 1,
            $firstContentStartPointer - 1
        );

        $requiredLinesCountBeforeFirstContent = SniffSettingsHelper::normalizeInteger(
            $this->linesCountBeforeFirstContent
        );
        $linesCountBeforeFirstContent = \max(
            \substr_count($whitespaceBeforeFirstContent, (string)$phpcsFile->eolChar) - 1,
            0
        );
        if ($linesCountBeforeFirstContent === $requiredLinesCountBeforeFirstContent) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            \sprintf(
                'Expected %d line%s before first content, found %d.',
                $requiredLinesCountBeforeFirstContent,
                $requiredLinesCountBeforeFirstContent === 1 ? '' : 's',
                $linesCountBeforeFirstContent
            ),
            $firstContentStartPointer,
            self::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTENT
        );

        if ($fix === false) {
            return;
        }

        $indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenerPointer);

        $phpcsFile->fixer->beginChangeset();

        $phpcsFile->fixer->replaceToken($docCommentOpenerPointer, '/**' . $phpcsFile->eolChar);
        for ($i = $docCommentOpenerPointer + 1; $i < $firstContentStartPointer; $i++) {
            $phpcsFile->fixer->replaceToken($i, '');
        }

        for ($i = 1; $i <= $requiredLinesCountBeforeFirstContent; $i++) {
            $phpcsFile->fixer->addContent(
                $docCommentOpenerPointer,
                \sprintf('%s *%s', $indentation, $phpcsFile->eolChar)
            );
        }

        $phpcsFile->fixer->addContentBefore($firstContentStartPointer, $indentation . ' * ');

        $phpcsFile->fixer->endChangeset();
    }

    /**
     * @param \SlevomatCodingStandard\Helpers\Annotation\Annotation[][] $annotationsGroups
     */
    private function checkLinesBetweenAnnotationsGroups(
        File $phpcsFile,
        int $docCommentOpenerPointer,
        array $annotationsGroups
    ): void {
        $tokens = $phpcsFile->getTokens();

        $requiredLinesCountBetweenAnnotationsGroups = SniffSettingsHelper::normalizeInteger(
            $this->linesCountBetweenAnnotationsGroups
        );

        $previousAnnotationsGroup = null;
        foreach ($annotationsGroups as $annotationsGroup) {
            if ($previousAnnotationsGroup === null) {
                $previousAnnotationsGroup = $annotationsGroup;

                continue;
            }

            $lastAnnotationInPreviousGroup = $previousAnnotationsGroup[(int)\count($previousAnnotationsGroup) - 1];
            $firstAnnotationInActualGroup = $annotationsGroup[0];

            $actualLinesCountBetweenGroups = $tokens[$firstAnnotationInActualGroup->getStartPointer()]['line'] -
                $tokens[$lastAnnotationInPreviousGroup->getEndPointer()]['line'] - 1;
            if ($actualLinesCountBetweenGroups === $requiredLinesCountBetweenAnnotationsGroups) {
                $previousAnnotationsGroup = $annotationsGroup;

                continue;
            }

            $fix = $phpcsFile->addFixableError(
                \sprintf(
                    'Expected %d line%s between annotations groups, found %d.',
                    $requiredLinesCountBetweenAnnotationsGroups,
                    $requiredLinesCountBetweenAnnotationsGroups === 1 ? '' : 's',
                    $actualLinesCountBetweenGroups
                ),
                $firstAnnotationInActualGroup->getStartPointer(),
                self::CODE_INCORRECT_LINES_COUNT_BETWEEN_ANNOTATIONS_GROUPS
            );

            if ($fix === false) {
                $previousAnnotationsGroup = $annotationsGroup;

                continue;
            }

            $indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenerPointer);

            $phpcsFile->fixer->beginChangeset();

            $endPointer = $lastAnnotationInPreviousGroup->getEndPointer();
            $startPointer = $firstAnnotationInActualGroup->getStartPointer();
            $phpcsFile->fixer->addNewline($endPointer);
            for ($i = $endPointer + 1; $i < $startPointer; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }

            for ($i = 1; $i <= $requiredLinesCountBetweenAnnotationsGroups; $i++) {
                $phpcsFile->fixer->addContent(
                    $endPointer,
                    \sprintf('%s *%s', $indentation, $phpcsFile->eolChar)
                );
            }

            $phpcsFile->fixer->addContentBefore(
                $firstAnnotationInActualGroup->getStartPointer(),
                $indentation . ' * '
            );

            $phpcsFile->fixer->endChangeset();
        }
    }

    private function checkLinesBetweenDescriptionAndFirstAnnotation(
        File $phpcsFile,
        int $docCommentOpenerPointer,
        int $firstContentStartPointer,
        int $firstContentEndPointer,
        ?Annotation $firstAnnotation = null
    ): void {
        if ($firstAnnotation === null) {
            return;
        }

        if ($firstContentStartPointer === $firstAnnotation->getStartPointer()) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        \preg_match('~(\\s+)$~', (string)$tokens[$firstContentEndPointer]['content'], $matches);

        $whitespaceBetweenDescriptionAndFirstAnnotation = $matches[1] ?? '';
        $whitespaceBetweenDescriptionAndFirstAnnotation .= TokenHelper::getContent(
            $phpcsFile,
            $firstContentEndPointer + 1,
            $firstAnnotation->getStartPointer() - 1
        );

        $requiredLinesCountBetweenDescriptionAndAnnotations = SniffSettingsHelper::normalizeInteger(
            $this->linesCountBetweenDescriptionAndAnnotations
        );
        $linesCountBetweenDescriptionAndAnnotations = \max(
            \substr_count($whitespaceBetweenDescriptionAndFirstAnnotation, (string)$phpcsFile->eolChar) - 1,
            0
        );
        if ($linesCountBetweenDescriptionAndAnnotations === $requiredLinesCountBetweenDescriptionAndAnnotations) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            \sprintf(
                'Expected %d line%s between description and annotations, found %d.',
                $requiredLinesCountBetweenDescriptionAndAnnotations,
                $requiredLinesCountBetweenDescriptionAndAnnotations === 1 ? '' : 's',
                $linesCountBetweenDescriptionAndAnnotations
            ),
            $firstAnnotation->getStartPointer(),
            self::CODE_INCORRECT_LINES_COUNT_BETWEEN_DESCRIPTION_AND_ANNOTATIONS
        );

        if ($fix === false) {
            return;
        }

        $indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenerPointer);

        $phpcsFile->fixer->beginChangeset();

        $phpcsFile->fixer->addNewline($firstContentEndPointer);
        for ($i = $firstContentEndPointer + 1; $i < $firstAnnotation->getStartPointer(); $i++) {
            $phpcsFile->fixer->replaceToken($i, '');
        }

        for ($i = 1; $i <= $requiredLinesCountBetweenDescriptionAndAnnotations; $i++) {
            $phpcsFile->fixer->addContent(
                $firstContentEndPointer,
                \sprintf('%s *%s', $indentation, $phpcsFile->eolChar)
            );
        }

        $phpcsFile->fixer->addContentBefore($firstAnnotation->getStartPointer(), $indentation . ' * ');

        $phpcsFile->fixer->endChangeset();
    }

    /**
     * @param \SlevomatCodingStandard\Helpers\Annotation\Annotation[] $annotations
     */
    private function checkLinesBetweenDifferentAnnotationsTypes(
        File $phpcsFile,
        int $docCommentOpenerPointer,
        array $annotations
    ): void {
        $requiredLinesCountBetweenDifferentAnnotationsTypes = SniffSettingsHelper::normalizeInteger(
            $this->linesCountBetweenDifferentAnnotationsTypes
        );

        $tokens = $phpcsFile->getTokens();

        $indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenerPointer);

        $previousAnnotation = null;
        foreach ($annotations as $annotation) {
            if ($previousAnnotation === null) {
                $previousAnnotation = $annotation;

                continue;
            }

            if ($annotation->getName() === $previousAnnotation->getName()) {
                $previousAnnotation = $annotation;

                continue;
            }

            \preg_match('~(\\s+)$~', (string)$tokens[$previousAnnotation->getEndPointer()]['content'], $matches);

            $linesCountAfterPreviousAnnotation = $matches[1] ?? '';
            $linesCountAfterPreviousAnnotation .= TokenHelper::getContent(
                $phpcsFile,
                $previousAnnotation->getEndPointer() + 1,
                $annotation->getStartPointer() - 1
            );

            $linesCountAfterPreviousAnnotation = \max(
                \substr_count(
                    $linesCountAfterPreviousAnnotation,
                    (string)$phpcsFile->eolChar
                ) - 1,
                0
            );

            if ($linesCountAfterPreviousAnnotation === $requiredLinesCountBetweenDifferentAnnotationsTypes) {
                $previousAnnotation = $annotation;

                continue;
            }

            $fix = $phpcsFile->addFixableError(
                \sprintf(
                    'Expected %d line%s between different annotations types, found %d.',
                    $requiredLinesCountBetweenDifferentAnnotationsTypes,
                    $requiredLinesCountBetweenDifferentAnnotationsTypes === 1 ? '' : 's',
                    $linesCountAfterPreviousAnnotation
                ),
                $annotation->getStartPointer(),
                self::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_ANNOTATIONS_TYPES
            );

            if ($fix === false) {
                $previousAnnotation = $annotation;

                continue;
            }

            $phpcsFile->fixer->beginChangeset();

            $phpcsFile->fixer->addNewline($previousAnnotation->getEndPointer());
            for ($i = $previousAnnotation->getEndPointer() + 1; $i < $annotation->getStartPointer(); $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }

            for ($i = 1; $i <= $requiredLinesCountBetweenDifferentAnnotationsTypes; $i++) {
                $phpcsFile->fixer->addContent(
                    $previousAnnotation->getEndPointer(),
                    \sprintf('%s *%s', $indentation, $phpcsFile->eolChar)
                );
            }

            $phpcsFile->fixer->addContentBefore($annotation->getStartPointer(), $indentation . ' * ');

            $phpcsFile->fixer->endChangeset();
        }
    }

    /**
     * @return string[][]
     */
    private function getAnnotationsGroups(): array
    {
        if ($this->normalizedAnnotationsGroups === null) {
            $this->normalizedAnnotationsGroups = [];
            foreach ($this->annotationsGroups as $annotationsGroup) {
                $this->normalizedAnnotationsGroups[] = SniffSettingsHelper::normalizeArray(\explode(
                    ',',
                    $annotationsGroup
                ));
            }
        }

        return $this->normalizedAnnotationsGroups;
    }

    private function isAnnotationMatched(Annotation $annotation, string $annotationName): bool
    {
        if ($annotation->getName() === $annotationName) {
            return true;
        }

        return $this->isAnnotationNameInAnnotationNamespace($annotationName, $annotation->getName());
    }

    private function isAnnotationNameInAnnotationNamespace(string $annotationNamespace, string $annotationName): bool
    {
        return $this->isAnnotationStartedFrom($annotationNamespace, $annotationName)
            || (
                \in_array(\substr($annotationNamespace, -1), ['\\', '-', ':'], true)
                && \str_starts_with($annotationName, $annotationNamespace)
            );
    }

    private function isAnnotationStartedFrom(string $annotationNamespace, string $annotationName): bool
    {
        return \str_ends_with($annotationNamespace, '*')
            && \str_starts_with($annotationName, \substr($annotationNamespace, 0, -1));
    }

    /**
     * @param \SlevomatCodingStandard\Helpers\Annotation\Annotation[] $annotations
     *
     * @return \SlevomatCodingStandard\Helpers\Annotation\Annotation[][]
     */
    private function sortAnnotationsToGroups(array $annotations): array
    {
        $expectedAnnotationsGroups = $this->getAnnotationsGroups();

        $sortedAnnotationsGroups = [];
        $annotationsNotInAnyGroup = [];
        foreach ($annotations as $annotation) {
            foreach ($expectedAnnotationsGroups as $annotationsGroupPosition => $annotationsGroup) {
                foreach ($annotationsGroup as $annotationName) {
                    if ($this->isAnnotationMatched($annotation, $annotationName)) {
                        $sortedAnnotationsGroups[$annotationsGroupPosition][] = $annotation;

                        continue 3;
                    }
                }
            }

            $annotationsNotInAnyGroup[] = $annotation;
        }

        \ksort($sortedAnnotationsGroups);

        foreach (\array_keys($sortedAnnotationsGroups) as $annotationsGroupPosition) {
            $expectedGroupOrder = \array_flip($expectedAnnotationsGroups[$annotationsGroupPosition]);
            \usort(
                $sortedAnnotationsGroups[$annotationsGroupPosition],
                function (Annotation $firstAnnotation, Annotation $secondAnnotation) use ($expectedGroupOrder): int {
                    $getExpectedOrder = function (string $annotationName) use ($expectedGroupOrder): int {
                        if (\array_key_exists($annotationName, $expectedGroupOrder)) {
                            return $expectedGroupOrder[$annotationName];
                        }

                        foreach ($expectedGroupOrder as $expectedAnnotationName => $expectedAnnotationOrder) {
                            if ($this->isAnnotationNameInAnnotationNamespace(
                                $expectedAnnotationName,
                                $annotationName
                            )) {
                                return $expectedAnnotationOrder;
                            }
                        }

                        return 0;
                    };

                    $expectedOrder = $getExpectedOrder($firstAnnotation->getName())
                        <=> $getExpectedOrder($secondAnnotation->getName());

                    return $expectedOrder !== 0
                        ? $expectedOrder
                        : $firstAnnotation->getStartPointer() <=> $secondAnnotation->getStartPointer();
                }
            );
        }

        if (\count($annotationsNotInAnyGroup) > 0) {
            $sortedAnnotationsGroups[] = $annotationsNotInAnyGroup;
        }

        return $sortedAnnotationsGroups;
    }
}
