<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use Nette\Utils\Strings;
use PhpParser\Comment;
use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \EonX\EasyQuality\Tests\Rector\SingleLineCommentRector\SingleLineCommentRectorTest
 */
final class SingleLineCommentRector extends AbstractRector
{
    /**
     * @var string[]
     */
    public $disallowedEnd = ['...', '.', ',', '?', ':', '!'];

    /**
     * @var string[]
     */
    public $ignoredPatterns = ['#^phpcs:#'];

    private bool $hasChanged;

    public function getNodeTypes(): array
    {
        return [Node::class];
    }

    /**
     * @noinspection AutoloadingIssuesInspection
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

        return $this->hasChanged ? $node : null;
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
            if (Strings::startsWith($oldCommentText, '/*')) {
                $newComments[] = $comment;
                continue;
            }

            $commentText = Strings::trim(Strings::replace($oldCommentText, '#^\/\/#', ''));

            if ($isMultilineComment === false && $this->isCommentIgnored($commentText) === false) {
                $commentText = Strings::firstUpper($commentText);
            }

            if (isset($comments[$index + 1])) {
                $nextCommentText = $comments[$index + 1];
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
                $commentText = Strings::replace($commentText, $pattern, '');
            }

            $comment = $this->getNewCommentIfChanged($comment, '// ' . $commentText);

            $newComments[] = $comment;
        }

        return $newComments;
    }

    private function isCommentIgnored(string $docLineContent): bool
    {
        foreach ($this->ignoredPatterns as $value) {
            if (Strings::match($docLineContent, $value)) {
                return true;
            }
        }

        return false;
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

    private function checkLineEndingDisallowed(string $docLineContent): ?string
    {
        $result = null;

        foreach ($this->disallowedEnd as $value) {
            $isLineEndingWithDisallowed = Strings::endsWith($docLineContent, $value);
            if ($isLineEndingWithDisallowed) {
                $result = $value;
                break;
            }
        }

        return $result;
    }
}
