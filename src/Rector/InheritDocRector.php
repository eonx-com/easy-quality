<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \EonX\EasyQuality\Tests\Rector\InheritDocRector\InheritDocRectorTest
 */
final class InheritDocRector extends AbstractRector
{
    /**
     * @var string
     */
    private const INHERITDOC_CORRECT_ANNOTATION = '{@inheritDoc}';

    /**
     * @var string
     */
    private const INHERITDOC_INCORRECT_ANNOTATION = '{@inheritdoc}';

    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replaces {@inheritdoc} annotation with {@inheritDoc}',
            [
                new CodeSample(
                    <<<'PHP'
/**
 * {@inheritdoc}
*/
public function someMethod(): void
{
}
PHP
                    ,
                    <<<'PHP'
/**
 * {@inheritDoc}
*/
public function someMethod(): void
{
}
PHP
                ),
            ]
        );
    }

    /**
     * @param ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
        /** @var \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo $phpDocInfo */
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);

        $children = $phpDocInfo->getPhpDocNode()->children;

        foreach ($children as $key => $child) {
            if ((string)$child->getAttribute('orig_node')  === self::INHERITDOC_INCORRECT_ANNOTATION) {
                $children[$key] = new PhpDocTextNode(self::INHERITDOC_CORRECT_ANNOTATION);
                $phpDocInfo->getPhpDocNode()->children = $children;
                return $node;
            }
        }

        return null;
    }
}
