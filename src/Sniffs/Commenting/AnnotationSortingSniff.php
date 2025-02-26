<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\Annotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

final class AnnotationSortingSniff implements Sniff
{
    /**
     * @var string
     */
    public const CODE_ANNOTATION_ALWAYS_TOP = 'AlwaysTopAnnotation';

    /**
     * @var string
     */
    public const CODE_ANNOTATION_SORT_ALPHABETICALLY = 'AnnotationSortAlphabetically';

    /**
     * @var string[]
     */
    public array $alwaysTopAnnotations = [];

    private File $phpcsFile;

    /**
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        if (DocCommentHelper::isInline($phpcsFile, $stackPtr)) {
            return;
        }

        $this->phpcsFile = $phpcsFile;

        $tokens = $phpcsFile->getTokens();
        $commentCloser = $tokens[$stackPtr]['comment_closer'];

        $fcStartPointer = TokenHelper::findNextExcluding(
            $phpcsFile,
            [\T_DOC_COMMENT_WHITESPACE, \T_DOC_COMMENT_STAR],
            $stackPtr + 1,
            $commentCloser
        );

        if ($fcStartPointer === null) {
            return;
        }

        $this->checkAnnotationsAreSorted($stackPtr);
    }

    public function register(): array
    {
        return [\T_DOC_COMMENT_OPEN_TAG];
    }

    private function checkAnnotationsAreSorted(int $openPointer): void
    {
        $annotations = AnnotationHelper::getAnnotations($this->phpcsFile, $openPointer);

        if (\count($annotations) === 0) {
            return;
        }

        $previousAnnotation = null;

        foreach ($annotations as $annotation) {
            $currentAnnotation = $this->getAnnotationName($annotation);
            if ($previousAnnotation === null) {
                $previousAnnotation = $currentAnnotation;

                continue;
            }

            // Previous is always top. Current is not. Do nothing
            if (\in_array($previousAnnotation, $this->alwaysTopAnnotations, true) === true &&
                \in_array($currentAnnotation, $this->alwaysTopAnnotations, true) === false) {
                $previousAnnotation = $currentAnnotation;

                continue;
            }

            $alwaysTop = $this->checkAnnotationsShouldBeOnTop(
                $previousAnnotation,
                $currentAnnotation,
                $annotation->getStartPointer()
            );

            // Current is always top. Current is not. Should switch
            if ($alwaysTop === true) {
                $previousAnnotation = $currentAnnotation;

                continue;
            }

            $this->compareAnnotationsAndAddError(
                $previousAnnotation,
                $currentAnnotation,
                $annotation->getStartPointer()
            );
            $previousAnnotation = $currentAnnotation;
        }
    }

    private function checkAnnotationsShouldBeOnTop(
        string $previousAnnotation,
        string $currentAnnotation,
        int $currentPointer
    ): bool {
        // Current is always top. Previous is not
        if (\in_array($previousAnnotation, $this->alwaysTopAnnotations, true) === false &&
            \in_array($currentAnnotation, $this->alwaysTopAnnotations, true) === true) {
            $this->phpcsFile->addError(
                \sprintf(
                    'Always on top annotations (%s) should be placed above other annotations' .
                    ', found "%s" is before "%s".',
                    \implode(', ', $this->alwaysTopAnnotations),
                    $previousAnnotation,
                    $currentAnnotation
                ),
                $currentPointer,
                self::CODE_ANNOTATION_ALWAYS_TOP
            );

            return true;
        }

        return false;
    }

    private function compareAnnotationsAndAddError(
        string $prevAnnotation,
        string $currAnnotation,
        int $currentPointer
    ): void {
        if (\strcasecmp($prevAnnotation, $currAnnotation) <= 0) {
            return;
        }

        $this->phpcsFile->addError(
            \sprintf(
                'Expected annotations should be alphabetically sorted, found "%s" is before "%s".',
                $prevAnnotation,
                $currAnnotation
            ),
            $currentPointer,
            self::CODE_ANNOTATION_SORT_ALPHABETICALLY
        );
    }

    /**
     * @param \SlevomatCodingStandard\Helpers\Annotation<\PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode> $annotation
     */
    private function getAnnotationName(Annotation $annotation): string
    {
        $exploded = \explode('\\', $annotation->getName());

        return \reset($exploded);
    }
}
