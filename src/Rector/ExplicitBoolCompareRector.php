<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\If_;
use PHPStan\Type\BooleanType;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @codeCoverageIgnore
 */
final class ExplicitBoolCompareRector extends AbstractRector
{
    /**
     * {@inheritDoc}
     */
    public function getNodeTypes(): array
    {
        return [If_::class, ElseIf_::class];
    }

    /**
     * @noinspection AutoloadingIssuesInspection
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

    /**
     * {@inheritDoc}
     */
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

        if ($this->nodeTypeResolver->isStaticType($conditionNode, BooleanType::class) === false) {
            return null;
        }

        $ifNode->cond = $this->getNewConditionNode($isNegated, $conditionNode);

        return $ifNode;
    }

    /**
     * Returns new condition node.
     */
    private function getNewConditionNode(bool $isNegated, Expr $expr): Expr
    {
        /** @var \PhpParser\Node\Expr\BinaryOp\Identical $identicalExpr */
        $identicalExpr = $expr;

        if ($isNegated === false && isset($identicalExpr->left, $identicalExpr->right)) {
            $left = $identicalExpr->left;
            /** @var \PhpParser\Node\Expr\ConstFetch $right */
            $right = $identicalExpr->right;

            if ($this->isValidNotNegated($left, $right) && (\mb_strtolower((string)$right->name) === 'true')) {
                return $left;
            }
        }

        if ($isNegated === true) {
            return new Expr\BinaryOp\Identical($expr, $this->nodeFactory->createFalse());
        }

        return $expr;
    }

    /**
     * Returns true if left and right is valid not negated nodes.
     *
     * @param mixed $left
     * @param mixed $right
     */
    private function isValidNotNegated($left, $right): bool
    {
        return ($left instanceof FuncCall || $left instanceof MethodCall || $left instanceof Instanceof_) &&
            ($right instanceof Expr\ConstFetch);
    }
}
