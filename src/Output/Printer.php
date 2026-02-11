<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Output;

use Override;
use PhpParser\Node\Expr\Array_;
use PhpParser\PrettyPrinter\Standard;

final class Printer extends Standard
{
    /**
     * @param \PhpParser\Node[] $stmts Array of statements
     */
    public function printNodes(array $stmts): string
    {
        $this->origTokens = null;

        return \ltrim($this->handleMagicTokens($this->pStmts($stmts, false)));
    }

    public function setStartIndentLevel(int $level): void
    {
        $this->setIndentLevel($level);
    }

    #[Override]
    protected function pExpr_Array(Array_ $node): string
    {
        $syntax = $node->getAttribute(
            'kind',
            $this->shortArraySyntax ? Array_::KIND_SHORT : Array_::KIND_LONG
        );

        if ($syntax === Array_::KIND_SHORT) {
            return '[' . $this->pMaybeMultiline($node->items, true) . ']';
        }

        return 'array(' . $this->pMaybeMultiline($node->items, true) . ')';
    }

    /**
     * @param \PhpParser\Node[] $nodes
     */
    #[Override]
    protected function pMaybeMultiline(array $nodes, ?bool $trailingComma = null): string
    {
        $trailingComma ??= false;

        if ($this->hasMultiLineNodes($nodes) === false) {
            return $this->pCommaSeparated($nodes);
        }

        return $this->pCommaSeparatedMultiline($nodes, $trailingComma) . $this->nl;
    }

    /**
     * @param \PhpParser\Node[] $nodes
     */
    private function hasMultiLineNodes(array $nodes): bool
    {
        return \array_any($nodes, static fn ($node) => $node->hasAttribute('multiLine'));
    }
}
