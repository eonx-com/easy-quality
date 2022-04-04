<?php
declare (strict_types=1);

namespace EonX\EasyQuality\Rector\Order;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use Rector\NodeNameResolver\NodeNameResolver;

/**
 * @see https://github.com/rectorphp/rector/blob/0.12.5/rules/Order/StmtOrder.php
 */
final class StmtOrder
{
    private NodeNameResolver $nodeNameResolver;

    public function __construct(NodeNameResolver $nodeNameResolver)
    {
        $this->nodeNameResolver = $nodeNameResolver;
    }

    /**
     * @param array<int, string> $desiredStmtOrder
     * @param array<int, string> $currentStmtOrder
     * @return array<int, int>
     */
    public function createOldToNewKeys(array $desiredStmtOrder, array $currentStmtOrder) : array
    {
        $newKeys = [];
        foreach ($desiredStmtOrder as $singleDesiredStmtOrder) {
            foreach ($currentStmtOrder as $currentKey => $classMethodName) {
                if ($classMethodName === $singleDesiredStmtOrder) {
                    $newKeys[] = $currentKey;
                }
            }
        }
        $oldKeys = \array_values($newKeys);
        \sort($oldKeys);
        /** @var array<int, int> $oldToNewKeys */
        return \array_combine($oldKeys, $newKeys);
    }

    /**
     * @param array<int, int> $oldToNewKeys
     */
    public function reorderClassStmtsByOldToNewKeys(ClassLike $classLike, array $oldToNewKeys) : void
    {
        $reorderedStmts = [];
        $stmtCount = \count($classLike->stmts);
        foreach ($classLike->stmts as $key => $stmt) {
            if (!\array_key_exists($key, $oldToNewKeys)) {
                $reorderedStmts[$key] = $stmt;
                continue;
            }
            // reorder here
            $newKey = $oldToNewKeys[$key];
            $reorderedStmts[$key] = $classLike->stmts[$newKey];
        }
        for ($iteration = 0; $iteration < $stmtCount; ++$iteration) {
            if (!\array_key_exists($iteration, $reorderedStmts)) {
                continue;
            }
            $classLike->stmts[$iteration] = $reorderedStmts[$iteration];
        }
    }

    /**
     * @param class-string<Node> $type
     * @return array<int, string>
     */
    public function getStmtsOfTypeOrder(ClassLike $classLike, string $type) : array
    {
        $stmtsByPosition = [];
        foreach ($classLike->stmts as $position => $classStmt) {
            if (!\is_a($classStmt, $type)) {
                continue;
            }
            $name = $this->nodeNameResolver->getName($classStmt);
            if ($name === null) {
                continue;
            }
            $stmtsByPosition[$position] = $name;
        }
        return $stmtsByPosition;
    }
}
