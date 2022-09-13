<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Node;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Core\Php\PhpVersionProvider;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \EonX\EasyQuality\Tests\Rector\RestoreDefaultNullToNullableTypeParameterRector\RestoreDefaultNullToNullableTypeParameterRectorTest
 *
 * @deprecated since 3.1, will be removed in 4.0. Use \EonX\EasyQuality\Sniffs\Functions\DisallowNonNullDefaultValueSniff instead.
 */
final class RestoreDefaultNullToNullableTypeParameterRector extends AbstractRector
{
    public function __construct(private PhpVersionProvider $phpVersionProvider)
    {
    }

    /**
     * @noinspection AutoloadingIssuesInspection
     */

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add null default to function arguments with PHP 7.1 nullable type', [
            new CodeSample(
                <<<'PHP'
class SomeClass
{
    public function __construct(?string $value)
    {
         $this->value = $value;
    }
}
PHP
                ,
                <<<'PHP'
class SomeClass
{
    public function __construct(?string $value = null)
    {
         $this->value = $value;
    }
}
PHP
            ),
        ]);
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->phpVersionProvider->isAtLeastPhpVersion(7) === false) {
            return null;
        }

        $hasChanged = false;
        foreach ($node->params as $param) {
            if ($this->shouldSkip($param)) {
                continue;
            }
            $param->default = $this->nodeFactory->createNull();
            $hasChanged = true;
        }

        return $hasChanged ? $node : null;
    }

    private function shouldSkip(Param $param): bool
    {
        if ($param->type instanceof NullableType === false) {
            return true;
        }

        return $param->default !== null;
    }
}
