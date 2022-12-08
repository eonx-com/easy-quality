<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Node;
use PhpParser\Node\AttributeGroup;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocChildNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \EonX\EasyQuality\Tests\Rector\PhpDocCommentRector\PhpDocCommentRectorTest
 */
final class PhpDocCommentRector extends AbstractRector
{
    /**
     * @var string
     */
    public const CONFIGURATION_ALLOWED_END = 'allowed_end';

    /**
     * @var string[]
     */
    private static array $allowedEnd = [
        '.',
        ',',
        '?',
        '!',
        ':',
        ')',
        '(',
        '}',
        '{',
        ']',
        '[',
    ];

    private int $currentIndex;

    private bool $hasChanged = false;

    private bool $isMultilineTagNode = false;

    private bool $isMultilineTextNode = false;

    private PhpDocInfo $phpDocInfo;

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        self::$allowedEnd = $configuration[self::CONFIGURATION_ALLOWED_END] ?? [];
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
            'Corrects comments in annotations',
            [
                new CodeSample(
                    <<<'PHP'
/**
 * some class
 */
class SomeClass
{
}
PHP
                    ,
                    <<<'PHP'
/**
 * Some class.
 */
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
        $this->hasChanged = false;

        if ($node instanceof AttributeGroup === false && $node->hasAttribute(AttributeKey::PHP_DOC_INFO)) {
            $this->phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);
            $this->checkPhpDoc();
        }

        return $this->hasChanged ? $node : null;
    }

    private function checkGenericTagValueNode(PhpDocTagNode $phpDocTagNode): void
    {
        if ($this->isMultilineTagNode && \str_starts_with($phpDocTagNode->name, '@')) {
            return;
        }

        if ($phpDocTagNode->value instanceof GenericTagValueNode === false) {
            return;
        }

        $value = $phpDocTagNode->value;

        $checkLastLetter = \str_ends_with($value->value, ')');
        $checkFirstLetter = \str_starts_with($value->value, '(') || \str_starts_with($value->value, '\\');

        if ($checkFirstLetter && $checkLastLetter) {
            return;
        }

        $valueAsArray = \explode(')', $value->value);

        if (\count($valueAsArray) === 2) {
            if ($this->isLineEndingWithAllowed($valueAsArray[1])) {
                $valueAsArray[1] = \mb_substr($valueAsArray[1], 0, -1);
            }

            $valueAsArray[1] = \trim($valueAsArray[1]);
            $valueAsArray[1] = \mb_strtoupper(\mb_substr($valueAsArray[1], 0, 1)) . \mb_substr($valueAsArray[1], 1);

            $newValue = \implode(') ', $valueAsArray);

            if ($value->value !== $newValue) {
                $firstValueLetter = \mb_substr($value->value, 0, 1);

                $newName = $phpDocTagNode->name;

                if (\in_array($firstValueLetter, ['\\', '('], true) === false) {
                    $newName = $phpDocTagNode->name . ' ';
                }

                $this->phpDocInfo->getPhpDocNode()->children[$this->currentIndex] = new PhpDocTagNode(
                    $newName,
                    new GenericTagValueNode($newValue)
                );
                $this->hasChanged = true;
            }
        }
    }

    /**
     * @param mixed[] $children
     */
    private function checkIsMultilineNode(array $children): void
    {
        $phpDocChildNode = $children[$this->currentIndex];

        if ($phpDocChildNode instanceof PhpDocTextNode) {
            if ($this->isMultilineTagNode && \in_array($phpDocChildNode->text, ['', ')'], true)) {
                $this->isMultilineTagNode = false;
            }

            $nextChildren = $children[$this->currentIndex + 1] ?? null;

            if ($nextChildren === null) {
                $this->isMultilineTextNode = false;

                return;
            }

            if ($nextChildren instanceof PhpDocTextNode) {
                if ($nextChildren->text !== '') {
                    $this->isMultilineTextNode = true;
                }

                if ($nextChildren->text === '') {
                    $this->isMultilineTextNode = false;
                }
            }

            if ($nextChildren instanceof PhpDocTagNode) {
                $this->isMultilineTextNode = false;
            }
        }

        if ($phpDocChildNode instanceof PhpDocTagNode) {
            $value = $phpDocChildNode->value;
            $nextChildren = $children[$this->currentIndex + 1] ?? null;

            if ((isset($value->value) && $value->value === '') || $nextChildren === null) {
                $this->isMultilineTagNode = false;

                return;
            }

            if ($value instanceof GenericTagValueNode) {
                $containsEol = \str_contains($value->value, \PHP_EOL);
                $lastLetter = \mb_substr($value->value, -1, 1);
                if ($containsEol || \in_array($lastLetter, ['(', '{'], true)) {
                    $this->isMultilineTagNode = true;
                }
            }

            if ($nextChildren instanceof PhpDocTextNode) {
                if ($nextChildren->text !== '') {
                    $this->isMultilineTagNode = true;
                }

                if ($nextChildren->text === '') {
                    $this->isMultilineTagNode = false;
                }
            }
        }
    }

    private function checkPhpDoc(): void
    {
        $children = $this->phpDocInfo->getPhpDocNode()->children;

        foreach ($children as $index => $phpDocChildNode) {
            $this->currentIndex = $index;
            $this->checkIsMultilineNode($children);
            $this->checkPhpDocChildNode($phpDocChildNode);
        }
    }

    private function checkPhpDocChildNode(PhpDocChildNode $phpDocChildNode): void
    {
        if ($phpDocChildNode instanceof PhpDocTextNode) {
            $this->checkTextNode($phpDocChildNode);
        }

        if ($phpDocChildNode instanceof PhpDocTagNode) {
            $this->checkTagNode($phpDocChildNode);
        }
    }

    private function checkTagNode(PhpDocTagNode $phpDocTagNode): void
    {
        if ($phpDocTagNode->value instanceof GenericTagValueNode) {
            $this->checkGenericTagValueNode($phpDocTagNode);
        }

        if ($phpDocTagNode->value instanceof VarTagValueNode) {
            $this->checkVarTagValueNode($phpDocTagNode);
        }
    }

    private function checkTextNode(PhpDocTextNode $phpDocTextNode): void
    {
        if ($this->isMultilineTagNode || $phpDocTextNode->text === '') {
            return;
        }

        $text = \explode(\PHP_EOL, $phpDocTextNode->text);
        $firstKey = \array_key_first($text);
        $lastKey = \array_key_last($text);

        foreach ($text as $index => $value) {
            $text[$index] = \trim($value);
        }

        $text[$firstKey] = \mb_strtoupper(\mb_substr($text[$firstKey], 0, 1)) . \mb_substr($text[$firstKey], 1);

        if ($this->isMultilineTextNode === false && $this->isLineEndingWithAllowed($text[$lastKey]) === false) {
            $text[$lastKey] .= '.';
        }

        // We need to generate new text without "*" for comparison
        $newText = \implode(\PHP_EOL, $text);

        $originalNode = $phpDocTextNode->getAttribute('orig_node');
        if (
            $originalNode instanceof PhpDocTextNode
            && $newText !== $originalNode->text
        ) {
            $newText = \implode(\PHP_EOL . ' * ', $text);
            $phpDocTextNode = new PhpDocTextNode($newText);
            $this->phpDocInfo->getPhpDocNode()->children[$this->currentIndex] = $phpDocTextNode;
            $this->hasChanged = true;
        }
    }

    private function checkVarTagValueNode(PhpDocTagNode $phpDocTagNode): void
    {
        /** @var \PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode $varTagValueNode */
        $varTagValueNode = $phpDocTagNode->value;

        if ($varTagValueNode->description === '' || $varTagValueNode->variableName === '') {
            return;
        }

        $newDescription = \trim($varTagValueNode->description);
        $newDescription = \mb_strtoupper(\mb_substr($newDescription, 0, 1)) . \mb_substr($newDescription, 1);

        if ($this->isLineEndingWithAllowed($newDescription)) {
            $newDescription = \mb_substr($newDescription, 0, -1);
        }

        if ($newDescription !== $varTagValueNode->description) {
            $varTagValueNode = new VarTagValueNode(
                $varTagValueNode->type,
                $varTagValueNode->variableName,
                $newDescription
            );

            $this->phpDocInfo->getPhpDocNode()->children[$this->currentIndex] = new PhpDocTagNode(
                $phpDocTagNode->name,
                $varTagValueNode
            );
            $this->hasChanged = true;
        }
    }

    private function isLineEndingWithAllowed(string $docLineContent): bool
    {
        $lastCharacter = \mb_substr($docLineContent, -1);

        return \in_array($lastCharacter, self::$allowedEnd, true);
    }
}
