<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use EonX\EasyQuality\ValueObject\ReturnArrayToYield;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\PhpParser\NodeTransformer;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\ReturnArrayToYieldRectorTest
 */
final class ReturnArrayToYieldRector extends AbstractRector implements ConfigurableRectorInterface
{
    use PhpDocBlockTrait;

    /**
     * @var string
     */
    public const METHODS_TO_YIELDS = 'methods_to_yields';

    /**
     * @var ReturnArrayToYield[]
     */
    private $methodsToYields;

    /**
     * @var NodeTransformer
     */
    private $nodeTransformer;

    public function __construct(NodeTransformer $nodeTransformer)
    {
        $this->nodeTransformer = $nodeTransformer;
    }

    public function configure(array $configuration): void
    {
        $methodsToYields = $configuration[self::METHODS_TO_YIELDS] ?? [];
        Assert::allIsInstanceOf($methodsToYields, ReturnArrayToYield::class);
        $this->methodsToYields = $methodsToYields;
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
        return new RuleDefinition('Turns array return to yield in specific type and method', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return ['event' => 'callback'];
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return iterable<mixed>
     */
    public static function getSubscribedEvents(): iterable
    {
        yield 'event' => 'callback';
    }
}
CODE_SAMPLE
                ,
                [
                    self::METHODS_TO_YIELDS => [
                        new ReturnArrayToYield('EventSubscriberInterface', 'getSubscribedEvents'),
                    ],
                ]
            ),
        ]);
    }

    /**
     * @param ClassMethod $classMethod
     *
     * @throws \Rector\Core\Exception\ShouldNotHappenException
     */
    public function refactor(Node $classMethod): ?Node
    {
        $hasChanged = false;
        foreach ($this->methodsToYields as $methodToYield) {
            if (!$this->isObjectType($classMethod, $methodToYield->getObjectType())) {
                continue;
            }

            if (!$this->isName($classMethod, $methodToYield->getMethod())) {
                continue;
            }

            $arrayNode = $this->getReturnArrayNodeFromClassMethod($classMethod);
            if ($arrayNode !== null) {
                $this->transformArrayToYieldsOnClassMethod($classMethod, $arrayNode);
                $this->updateClassMethodPhpDocBlock($classMethod);

                $hasChanged = true;
            }
        }

        return $hasChanged ? $classMethod : null;
    }

    private function getReturnArrayNodeFromClassMethod(ClassMethod $classMethod): ?Array_
    {
        if ($classMethod->stmts === null) {
            return null;
        }

        foreach ($classMethod->stmts as $statement) {
            if ($statement instanceof Return_) {
                if (!$statement->expr instanceof Array_) {
                    continue;
                }

                $this->returnArrayNodePhpDocInfo = $statement->getAttribute(AttributeKey::PHP_DOC_INFO);
                $this->returnArrayNodeComments = $statement->getComments();

                return $statement->expr;
            }
        }

        return null;
    }

    /**
     * @throws \Rector\Core\Exception\ShouldNotHappenException
     */
    private function transformArrayToYieldsOnClassMethod(ClassMethod $classMethod, Array_ $array): void
    {
        $yieldNodes = $this->nodeTransformer->transformArrayToYields($array);

        $parentNode = $array->getAttribute(AttributeKey::PARENT_NODE);
        if ($parentNode === null) {
            throw new ShouldNotHappenException();
        }

        $classMethod->returnType = new Identifier('iterable');

        foreach ((array)$classMethod->stmts as $key => $classMethodStmt) {
            if (!$classMethodStmt instanceof Return_) {
                continue;
            }

            unset($classMethod->stmts[$key]);
        }

        $classMethod->stmts = \array_merge((array)$classMethod->stmts, $yieldNodes);
    }
}
