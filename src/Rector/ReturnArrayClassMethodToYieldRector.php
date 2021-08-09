<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Comment;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\Type\IterableType;
use PHPStan\Type\MixedType;
use PHPUnit\Framework\TestCase;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwarePhpDocTagNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\CodingStyle\ValueObject\ReturnArrayClassMethodToYield;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\PhpParser\NodeTransformer;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\ConfiguredCodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Webmozart\Assert\Assert;

/**
 * @see \EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\ReturnArrayClassMethodToYieldRectorTest
 */
final class ReturnArrayClassMethodToYieldRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const METHODS_TO_YIELDS = 'methods_to_yields';

    /**
     * @var ReturnArrayClassMethodToYield[]
     */
    private $methodsToYields;

    /**
     * @var NodeTransformer
     */
    private $nodeTransformer;

    /**
     * @var Comment[]
     */
    private $returnArrayNodeComments = [];

    /**
     * @var PhpDocInfo|null
     */
    private $returnArrayNodePhpDocInfo;

    public function __construct(NodeTransformer $nodeTransformer)
    {
        $this->nodeTransformer = $nodeTransformer;

        // default values
        $this->methodsToYields = [
            new ReturnArrayClassMethodToYield(TestCase::class, 'provide*'),
        ];
    }

    public function configure(array $configuration): void
    {
        $methodsToYields = $configuration[self::METHODS_TO_YIELDS] ?? [];
        Assert::allIsInstanceOf($methodsToYields, ReturnArrayClassMethodToYield::class);
        $this->methodsToYields = $methodsToYields;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Turns array return to yield return in specific type and method', [
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
    public static function getSubscribedEvents(): iterable
    {
        yield 'event' => 'callback';
    }
}
CODE_SAMPLE
                ,
                [
                    self::METHODS_TO_YIELDS => [
                        new ReturnArrayClassMethodToYield('EventSubscriberInterface', 'getSubscribedEvents'),
                    ],
                ]
            ),
        ]);
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
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
            if (!$this->isObjectType($classMethod, $methodToYield->getType())) {
                continue;
            }

            if (!$this->isName($classMethod, $methodToYield->getMethod())) {
                continue;
            }

            $arrayNode = $this->getReturnArrayNodeFromClassMethod($classMethod);
            if ($arrayNode === null) {
                continue;
            }

            $this->transformArrayToYieldsOnClassMethod($classMethod, $arrayNode);
            $this->updateClassMethodPhpDocBlock($classMethod);

            $hasChanged = true;
        }

        if (!$hasChanged) {
            return null;
        }

        return $classMethod;
    }

    private function completeComments(ClassMethod $classMethod): void
    {
        if ($this->returnArrayNodePhpDocInfo === null && $this->returnArrayNodeComments === []) {
            return;
        }

        $classMethod->setAttribute(AttributeKey::PHP_DOC_INFO, $this->returnArrayNodePhpDocInfo);
        $classMethod->setAttribute(AttributeKey::COMMENTS, $this->returnArrayNodeComments);
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

        // remove whole return node
        $parentNode = $array->getAttribute(AttributeKey::PARENT_NODE);
        if ($parentNode === null) {
            throw new ShouldNotHappenException();
        }

        // change return typehint
        $classMethod->returnType = new Identifier('iterable');

        foreach ((array)$classMethod->stmts as $key => $classMethodStmt) {
            if (!$classMethodStmt instanceof Return_) {
                continue;
            }

            unset($classMethod->stmts[$key]);
        }

        $classMethod->stmts = \array_merge((array)$classMethod->stmts, $yieldNodes);
    }

    private function updateClassMethodPhpDocBlock(ClassMethod $classMethod): void
    {
        /** @var PhpDocInfo|null $docComment */
        $docComment = $classMethod->getAttribute(AttributeKey::PHP_DOC_INFO);
        if ($classMethod->getDocComment() === null) {
            $classMethod->setDocComment(new Doc("/**\n * @return array\n */"));
            $docComment = $this->phpDocInfoFactory->createFromNode($classMethod);
        }

        $returnType = new IterableType(new MixedType(), new MixedType());
        $docComment->changeReturnType($returnType);

        $emptyLine = new AttributeAwarePhpDocTagNode('', new GenericTagValueNode(''));

        if ($this->returnArrayNodePhpDocInfo !== null) {
            if ($this->returnArrayNodePhpDocInfo->getPhpDocNode()->children !== []) {
                $docComment->addPhpDocTagNode($emptyLine);
            }

            foreach ($this->returnArrayNodePhpDocInfo->getPhpDocNode()->children as $children) {
                $newLine = new AttributeAwarePhpDocTagNode('', new GenericTagValueNode((string)$children));
                $docComment->addPhpDocTagNode($newLine);
            }
        }

        if (count($this->returnArrayNodeComments)){
            $isEmptyLineAdded = false;
            foreach ($this->returnArrayNodeComments as $nodeComment){
                if (\strpos($nodeComment->getText(), '/**') === false) {
                    if ($isEmptyLineAdded === false){
                        $docComment->addPhpDocTagNode($emptyLine);
                        $isEmptyLineAdded = true;
                    }

                    $commentText = \str_replace('//', '', $nodeComment->getText());
                    $newLine = new AttributeAwarePhpDocTagNode('', new GenericTagValueNode($commentText));
                    $docComment->addPhpDocTagNode($newLine);
                }
            }
        }

        $classMethod->setAttribute(AttributeKey::PHP_DOC_INFO, $docComment);
    }
}