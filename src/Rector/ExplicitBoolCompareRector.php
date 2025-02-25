<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\If_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ExplicitBoolCompareRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [If_::class, ElseIf_::class];
    }

    /**
     * @throws \Symplify\RuleDocGenerator\Exception\PoorDocumentationException
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Makes bool conditions more pretty', [
            new CodeSample(
                <<<'PHP'
final class SomeController
{
    public function run($items)
    {
        if (\is_array([]) === true) {
            return 'is array';
        }
    }
}
PHP
                ,
                <<<'PHP'
final class SomeController
{
    public function run($items)
    {
        if (\is_array([])) {
            return 'is array';
        }
    }
}
PHP
            ),
        ]);
    }

    public function refactor(Node $node): ?Node
    {
        /** @var \PhpParser\Node\Stmt\If_|\PhpParser\Node\Stmt\ElseIf_ $ifNode */
        $ifNode = $node;

        $conditionNode = $ifNode->cond;
        $isNegated = false;

        if ($ifNode->cond instanceof BooleanNot) {
            $conditionNode = $ifNode->cond->expr;
            $isNegated = true;
        }

        if ($this->nodeTypeResolver->getType($conditionNode)->isBoolean()->no()) {
            return null;
        }

        if ($isNegated === false && $conditionNode instanceof Identical) {
            $left = $conditionNode->left;
            $right = $conditionNode->right;

            if (
                ($left instanceof FuncCall || $left instanceof MethodCall || $left instanceof Instanceof_)
                && $right instanceof ConstFetch
                && $this->nodeTypeResolver->getType($left)->isBoolean()->yes()
                && (\mb_strtolower((string)$right->name) === 'true')
            ) {
                $ifNode->cond = $left;

                return $ifNode;
            }
        }

        if ($isNegated === true) {
            $ifNode->cond = new Identical($conditionNode, $this->nodeFactory->createFalse());

            return $ifNode;
        }

        return null;
    }
}
