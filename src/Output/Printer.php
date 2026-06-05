<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Output;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Scalar\String_;
use PhpParser\PrettyPrinter\Standard;

final class Printer extends Standard
{
    private ?string $originalCode = null;

    /**
     * @param \PhpParser\Node[] $stmts Array of statements
     */
    public function printNodes(array $stmts): string
    {
        $this->origTokens = null;

        return \ltrim($this->handleMagicTokens($this->pStmts($stmts, false)));
    }

    /**
     * Provide the exact source that was parsed so string literals can be reproduced
     * verbatim from their original positions, preserving the formatting of multi-line
     * values such as heredoc/nowdoc ICU messages.
     */
    public function setOriginalCode(string $code): void
    {
        $this->originalCode = $code;
    }

    public function setStartIndentLevel(int $level): void
    {
        $this->setIndentLevel($level);
    }

    protected function pExpr_Array(Array_ $node): string
    {
        $syntax = $node->getAttribute(
            'kind',
            $this->shortArraySyntax ? Array_::KIND_SHORT : Array_::KIND_LONG,
        );

        if ($syntax === Array_::KIND_SHORT) {
            return '[' . $this->pMaybeMultiline($node->items, true) . ']';
        }

        return 'array(' . $this->pMaybeMultiline($node->items, true) . ')';
    }

    /**
     * @param \PhpParser\Node[] $nodes
     */
    protected function pMaybeMultiline(array $nodes, ?bool $trailingComma = null): string
    {
        $trailingComma ??= false;

        if ($this->hasMultiLineNodes($nodes) === false) {
            return $this->pCommaSeparated($nodes);
        }

        return $this->pCommaSeparatedMultiline($nodes, $trailingComma) . $this->nl;
    }

    /**
     * Preserve the exact original source of string literals so multi-line values
     * (such as heredoc/nowdoc or quoted ICU messages) keep their original tabulation
     * instead of being re-escaped, re-indented or collapsed onto a single line.
     */
    protected function pScalar_String(String_ $node): string
    {
        $originalString = $this->getOriginalString($node);

        if ($originalString !== null) {
            return $originalString;
        }

        return parent::pScalar_String($node);
    }

    private function getOriginalString(String_ $node): ?string
    {
        if ($this->originalCode !== null) {
            $startFilePos = $node->getAttribute('startFilePos');
            $endFilePos = $node->getAttribute('endFilePos');

            if (\is_int($startFilePos) && \is_int($endFilePos) && $endFilePos >= $startFilePos) {
                return \substr($this->originalCode, $startFilePos, $endFilePos - $startFilePos + 1);
            }
        }

        // Fallback for plain quoted strings when the original source is unavailable.
        // The `rawValue` of a heredoc/nowdoc omits its delimiters, so it must not be used here
        $kind = $node->getAttribute('kind', String_::KIND_SINGLE_QUOTED);
        $rawValue = $node->getAttribute('rawValue');

        if (\is_string($rawValue)
            && ($kind === String_::KIND_SINGLE_QUOTED || $kind === String_::KIND_DOUBLE_QUOTED)) {
            return $rawValue;
        }

        return null;
    }

    /**
     * @param \PhpParser\Node[] $nodes
     */
    private function hasMultiLineNodes(array $nodes): bool
    {
        return \array_any($nodes, static fn($node) => $node->hasAttribute('multiLine'));
    }
}
