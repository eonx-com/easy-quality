<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Comment;
use PhpParser\Node;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \EonX\EasyQuality\Tests\Rector\SingleLineCommentRector\SingleLineCommentRectorTest
 */
final class SingleLineCommentRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const CONFIGURATION_DISALLOWED_END = 'disallowed_end';

    /**
     * @var string
     */
    public const CONFIGURATION_IGNORED_PATTERNS = 'ignored_patterns';

    /**
     * @var string[]
     */
    private static array $disallowedEnd = ['...', '.', ',', '?', ':', '!'];

    /**
     * @var string[]
     */
    private static array $ignoredPatterns = ['#^phpcs:#'];

    private bool $hasChanged;

    /**
     * @param array<array-key, string[]> $configuration
     */
    public function configure(array $configuration): void
    {
        self::$disallowedEnd = $configuration[self::CONFIGURATION_DISALLOWED_END] ?? self::$disallowedEnd;
        self::$ignoredPatterns = $configuration[self::CONFIGURATION_IGNORED_PATTERNS] ?? self::$ignoredPatterns;
    }

    public function getNodeTypes(): array
    {
        return [Node::class];
    }

    /**
     * @throws \Symplify\RuleDocGenerator\Exception\PoorDocumentationException
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Corrects single line comment',
            [
                new CodeSample(
                    <<<'PHP'
// some class.
class SomeClass
{
}
PHP
                    ,
                    <<<'PHP'
// Some class
class SomeClass
{
}
PHP
                ),
            ]
        );
    }

    public function refactor(Node $node): ?Node
    {
        $comments = $node->getComments();
        $this->hasChanged = false;

        if (\count($comments) !== 0) {
            $comments = $this->checkComments($comments);
            $node->setAttribute('comments', $comments);
        }

        return $this->hasChanged() ? $node : null;
    }

    /**
     * @param \PhpParser\Comment[] $comments
     *
     * @return \PhpParser\Comment[]
     */
    private function checkComments(array $comments): array
    {
        $newComments = [];
        $isMultilineComment = false;

        foreach ($comments as $index => $comment) {
            $oldCommentText = $comment->getText();
            if (\str_starts_with($oldCommentText, '/*')) {
                $newComments[] = $comment;

                continue;
            }

            $commentText = \preg_replace('#^\/\/#', '', $oldCommentText);

            if ($commentText === null) {
                continue;
            }

            $commentText = \trim($commentText);

            if ($isMultilineComment === false && $this->isCommentIgnored($commentText) === false) {
                $commentText = \mb_strtoupper(\mb_substr($commentText, 0, 1)) . \mb_substr($commentText, 1);
            }

            if (isset($comments[$index + 1])) {
                $nextCommentText = (string)$comments[$index + 1];
                if ($nextCommentText !== '') {
                    $isMultilineComment = true;
                }
            }

            if (isset($comments[$index + 1]) === false) {
                $isMultilineComment = false;
            }

            if ($isMultilineComment) {
                $comment = $this->getNewCommentIfChanged($comment, '// ' . $commentText);
                $newComments[] = $comment;

                continue;
            }

            $disallowEnding = $this->checkLineEndingDisallowed($commentText);

            if ($disallowEnding !== null) {
                $pattern = '#' . \preg_quote($disallowEnding, '#') . '$#';
                $commentText = \preg_replace($pattern, '', $commentText);
            }

            $comment = $this->getNewCommentIfChanged($comment, '// ' . $commentText);

            $newComments[] = $comment;
        }

        return $newComments;
    }

    private function checkLineEndingDisallowed(string $docLineContent): ?string
    {
        $result = null;

        foreach (self::$disallowedEnd as $value) {
            $isLineEndingWithDisallowed = \str_ends_with($docLineContent, $value);
            if ($isLineEndingWithDisallowed) {
                $result = $value;

                break;
            }
        }

        return $result;
    }

    private function getNewCommentIfChanged(Comment $comment, string $commentText): Comment
    {
        if ($comment->getText() !== $commentText) {
            $comment = new Comment(
                $commentText,
                $comment->getStartLine(),
                $comment->getStartFilePos(),
                $comment->getStartTokenPos(),
                $comment->getEndLine(),
                $comment->getEndFilePos(),
                $comment->getEndTokenPos()
            );

            $this->hasChanged = true;
        }

        return $comment;
    }

    private function hasChanged(): bool
    {
        return $this->hasChanged;
    }

    private function isCommentIgnored(string $docLineContent): bool
    {
        foreach (self::$ignoredPatterns as $ignoredPattern) {
            if (\preg_match($ignoredPattern, $docLineContent) === 1) {
                return true;
            }
        }

        return false;
    }
}
