<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use EonX\EasyQuality\Rector\ValueObject\ReturnArrayToYield;
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
     * @var \EonX\EasyQuality\Rector\ValueObject\ReturnArrayToYield[]
     */
    private iterable $methodsToYields;

    public function __construct(private readonly NodeTransformer $nodeTransformer)
    {
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

    /**
     * @throws \Symplify\RuleDocGenerator\Exception\ShouldNotHappenException
     * @throws \Symplify\RuleDocGenerator\Exception\PoorDocumentationException
     */
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
     * @param \PhpParser\Node\Stmt\ClassMethod $classMethod
     *
     * @throws \Rector\Core\Exception\ShouldNotHappenException
     */
    public function refactor(Node $classMethod): ?Node
    {
        $hasChanged = false;
        foreach ($this->methodsToYields as $methodToYield) {
            if ($this->isObjectType($classMethod, $methodToYield->getObjectType()) === false) {
                continue;
            }

            if ($this->isName($classMethod, $methodToYield->getMethod()) === false) {
                continue;
            }

            $arrayNode = $this->getReturnArrayNodeFromClassMethod($classMethod);
            if ($arrayNode !== null) {
                $this->transformArrayToYieldsOnClassMethod($classMethod, $arrayNode);

                if ($this->hasDocBlockInParentMethod($classMethod) === false) {
                    $this->updateClassMethodPhpDocBlock($classMethod);
                }

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
                if ($statement->expr instanceof Array_ === false) {
                    continue;
                }

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
            if ($classMethodStmt instanceof Return_ === false) {
                continue;
            }

            unset($classMethod->stmts[$key]);
        }

        $classMethod->stmts = \array_merge((array)$classMethod->stmts, $yieldNodes);
    }
}
