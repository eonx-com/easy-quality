<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class UselessSingleAnnotationRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const ANNOTATIONS = 'annotations';

    /**
     * @var string[]
     */
    private array $annotations = [];

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        $this->annotations = $configuration[self::ANNOTATIONS] ?? [];
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Removes PHPDoc completely if it contains only useless single annotation.',
            [
                new ConfiguredCodeSample(
                    <<<'PHP'
/**
 * {@inheritDoc}
*/
public function someMethod(): array
{
}
PHP
                    ,
                    <<<'PHP'
public function someMethod(): array
{
}
PHP
                    ,
                    [
                        self::ANNOTATIONS => ['{@inheritDoc}'],
                    ]
                ),
            ]
        );
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);

        $children = $phpDocInfo->getPhpDocNode()
            ->children;

        if (
            \count($children) === 1
            && $children[0]->getAttribute('orig_node') instanceof PhpDocTextNode
            && \in_array($children[0]->getAttribute('orig_node')->text, $this->annotations, true)
        ) {
            $phpDocInfo->getPhpDocNode()->children = [];
            $phpDocInfo->markAsChanged();

            return $node;
        }

        return null;
    }
}
