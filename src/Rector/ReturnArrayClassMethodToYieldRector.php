<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Comment;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Yield_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwarePhpDocTagNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwarePhpDocTextNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwareReturnTagValueNode;
use Rector\AttributeAwarePhpDoc\Ast\Type\AttributeAwareGenericTypeNode;
use Rector\AttributeAwarePhpDoc\Ast\Type\AttributeAwareIdentifierTypeNode;
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
            if ($arrayNode !== null) {
                $this->transformArrayToYieldsOnClassMethod($classMethod, $arrayNode);
                $this->updateClassMethodPhpDocBlock($classMethod);

                $hasChanged = true;
            }

            // @todo Refactor to fully support yield in foreach.
            $this->replaceReturnToYield($classMethod);
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

    private function replaceReturnToYield(ClassMethod $classMethod): void
    {
        if ($classMethod->stmts !== null) {
            foreach ($classMethod->stmts as $key => $statement) {
                if ($statement instanceof Return_) {
                    $this->returnArrayNodePhpDocInfo = $statement->getAttribute(AttributeKey::PHP_DOC_INFO);
                    $this->returnArrayNodeComments = $statement->getComments();

                    $classMethod->stmts[$key] = new Expression(new Yield_($statement->expr));
                    $classMethod->returnType = new Identifier('iterable');

                    $this->updateClassMethodPhpDocBlock($classMethod);
                }
            }
        }
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

    private function updateClassMethodPhpDocBlock(ClassMethod $classMethod): void
    {
        /** @var PhpDocInfo|null $docComment */
        $docComment = $classMethod->getAttribute(AttributeKey::PHP_DOC_INFO);
        $docCommentText = "/**";

        if ($classMethod->getDocComment() === null) {
            $docCommentText .= "\n * @return iterable<mixed>";
        } else {
            foreach ($docComment->getPhpDocNode()->children as $child) {
                if ($child instanceof AttributeAwarePhpDocTagNode) {
                    if ($child->value instanceof AttributeAwareReturnTagValueNode) {
                        $iterableTypes = [];
                        if (
                            $child->value->type instanceof AttributeAwareGenericTypeNode
                            && $child->value->type->type->name === 'iterable'
                            && isset($child->value->type->genericTypes)
                        ) {
                            foreach ($child->value->type->genericTypes as $genericType) {
                                $iterableTypes[] = $genericType->name;
                            }
                        } else {
                            $iterableTypes[] = 'mixed';
                        }
//                        var_dump($iterableTypes);
                        $docCommentText .= "\n * @return iterable<" . \implode( ', ', $iterableTypes) . '>';
                    } else {
                        $docCommentText .= "\n * $child->name $child->value";
                    }
                }

                if ($child instanceof AttributeAwarePhpDocTextNode) {
                    $docCommentText .= "\n *" . ($child->text ? ' ' . $child->text : '');
                }
            }
        }

        if ($this->returnArrayNodePhpDocInfo !== null
            && count($this->returnArrayNodePhpDocInfo->getPhpDocNode()->children) > 0) {
            $docCommentText .= "\n *";

            foreach ($this->returnArrayNodePhpDocInfo->getPhpDocNode()->children as $child) {
                $commentText = (string)$child;
                $docCommentText .= "\n *" . ($commentText ? ' ' . $commentText : '');
            }
        }

        if (count($this->returnArrayNodeComments) > 0) {
            // $firstLineAdded is really need, do NOT remove.
            $firstLineAdded = false;
            foreach ($this->returnArrayNodeComments as $nodeComment) {
                if (\strpos($nodeComment->getText(), '/**') === false) {
                    if ($firstLineAdded === false) {
                        $docCommentText .= "\n *";
                        $firstLineAdded = true;
                    }
                    $commentText = \preg_replace(['/^\/\/\s*/', '/^\s*/'], '', $nodeComment->getText());
                    $docCommentText .= "\n *" . ($commentText ? ' ' . $commentText : '');
                }
            }
        }

        $docCommentText .= "\n */";
        $classMethod->setDocComment(new Doc($docCommentText));
        $docComment = $this->phpDocInfoFactory->createFromNode($classMethod);
        $classMethod->setAttribute(AttributeKey::PHP_DOC_INFO, $docComment);
    }
}