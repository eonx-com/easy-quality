<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @codeCoverageIgnore
 */
final class StrictInArrayRector extends AbstractRector
{
    /**
     * {@inheritDoc}
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * {@inheritDoc}
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Makes in_array calls strict', [
            new CodeSample('in_array($value, $items);', 'in_array($value, $items, true);'),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function refactor(Node $node): ?Node
    {
        /** @var \PhpParser\Node\Expr\FuncCall $funcCall */
        $funcCall = $node;

        if ($this->isName($node, 'in_array') === false) {
            return null;
        }

        if (\count($funcCall->args) === 2) {
            $funcCall->args[2] = $this->nodeFactory->createArg($this->nodeFactory->createTrue());
        }

        return $node;
    }
}
