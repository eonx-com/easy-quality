<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use Nette\Utils\Strings;
use PhpParser\Node;
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
     * @var string[]
     */
    public $allowedEnd = ['.', ',', '?', '!', ':', ')', '(', '}', '{', ']', '['];

    /**
     * @var int
     */
    private $currentIndex;

    /**
     * @var bool
     */
    private $isMultilineTagNode = false;

    /**
     * @var bool
     */
    private $isMultilineTextNode = false;

    /**
     * @var PhpDocInfo
     */
    private $phpDocInfo;

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
        if ($node->hasAttribute(AttributeKey::PHP_DOC_INFO)) {
            $this->phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);
            $this->checkPhpDoc();
        }

        return $node;
    }

    private function checkGenericTagValueNode(PhpDocTagNode $phpDocTagNode): void
    {
        if ($this->isMultilineTagNode && Strings::startsWith($phpDocTagNode->name, '@')) {
            return;
        }

        /** @var GenericTagValueNode $value */
        $value = $phpDocTagNode->value;
        if (isset($value->value) === false) {
            return;
        }

        $checkLastLetter = Strings::endsWith($value->value, ')');
        $checkFirstLetter = Strings::startsWith($value->value, '(') || Strings::startsWith($value->value, '\\');

        if ($checkFirstLetter && $checkLastLetter) {
            return;
        }

        $valueAsArray = (array)\explode(')', $value->value);

        if (\count($valueAsArray) === 2) {
            if ($this->isLineEndingWithAllowed($valueAsArray[1])) {
                $valueAsArray[1] = Strings::substring($valueAsArray[1], 0, -1);
            }

            $valueAsArray[1] = Strings::firstUpper(Strings::trim($valueAsArray[1]));

            $newValue = \implode(') ', $valueAsArray);

            if ($value->value !== $newValue) {
                $firstValueLetter = Strings::substring($value->value, 0, 1);

                $newName = $phpDocTagNode->name;

                if (\in_array($firstValueLetter, ['\\', '('], true) === false) {
                    $newName = $phpDocTagNode->name . ' ';
                }

                $this->phpDocInfo->getPhpDocNode()->children[$this->currentIndex] = new PhpDocTagNode(
                    $newName,
                    new GenericTagValueNode($newValue)
                );
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
                $containsEol = Strings::contains($value->value, \PHP_EOL);
                $lastLetter = Strings::substring($value->value, -1, 1);
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

        $text = (array)\explode(\PHP_EOL, $phpDocTextNode->text);
        $firstKey = array_key_first($text);
        $lastKey = array_key_last($text);

        foreach ($text as $index => $value) {
            $text[$index] = Strings::trim($value);
        }

        $text[$firstKey] = Strings::firstUpper($text[$firstKey]);

        if ($this->isMultilineTextNode === false && $this->isLineEndingWithAllowed($text[$lastKey]) === false) {
            $text[$lastKey] .= '.';
        }

        $linesCount = \count($text);
        for ($iterator = 1; $iterator < $linesCount; $iterator++) {
            $text[$iterator] = ' * ' . $text[$iterator];
        }

        $newText = \implode(\PHP_EOL, $text);

        if ($phpDocTextNode->getAttribute('orig_node') !== null
            && $newText !== $phpDocTextNode->getAttribute('orig_node')->text) {
            $phpDocTextNode = new PhpDocTextNode($newText);
            $this->phpDocInfo->getPhpDocNode()->children[$this->currentIndex] = $phpDocTextNode;
        }
    }

    private function checkVarTagValueNode(PhpDocTagNode $phpDocTagNode): void
    {
        /** @var \PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode $varTagValueNode */
        $varTagValueNode = $phpDocTagNode->value;

        if ($varTagValueNode->description === '' || $varTagValueNode->variableName === '') {
            return;
        }

        $newDescription = Strings::firstUpper(Strings::trim($varTagValueNode->description));

        if ($this->isLineEndingWithAllowed($newDescription)) {
            $newDescription = Strings::substring($newDescription, 0, -1);
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
        }
    }

    private function isLineEndingWithAllowed(string $docLineContent): bool
    {
        $lastCharacter = Strings::substring($docLineContent, -1);

        return \in_array($lastCharacter, $this->allowedEnd, true);
    }
}
