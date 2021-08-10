<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use EonX\EasyQuality\ValueObject\PhpDocReturnForIterable;
use PhpParser\Comment;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwarePhpDocTagNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwarePhpDocTextNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwareReturnTagValueNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\PhpParser\NodeTransformer;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\ConfiguredCodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Webmozart\Assert\Assert;

/**
 * @see \EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\PhpDocReturnForIterableRectorTest
 */
final class PhpDocReturnForIterableRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var string
     */
    public const METHODS_TO_UPDATE = 'methods_to_update';

    /**
     * @var PhpDocReturnForIterable[]
     */
    private $methodsToUpdate;

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
        $methodsToUpdate = $configuration[self::METHODS_TO_UPDATE] ?? [];
        Assert::allIsInstanceOf($methodsToUpdate, PhpDocReturnForIterable::class);
        $this->methodsToUpdate = $methodsToUpdate;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Turns @return to @return iterable<mixed> in specific type and method', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return mixed[]
     */
    public static function getSubscribedEvents(): iterable
    {
        yield 'event' => 'callback';
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
                    self::METHODS_TO_UPDATE => [
                        new PhpDocReturnForIterable('EventSubscriberInterface', 'getSubscribedEvents'),
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
        foreach ($this->methodsToUpdate as $methodToUpdate) {
            if (!$this->isObjectType($classMethod, $methodToUpdate->getType())) {
                continue;
            }

            if (!$this->isName($classMethod, $methodToUpdate->getMethod())) {
                continue;
            }

            if ($classMethod->returnType->name === 'iterable'){
                $this->updateClassMethodPhpDocBlock($classMethod);
            }
            $hasChanged = true;
        }

        return $hasChanged ? $classMethod : null;
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
                        $docCommentText .= "\n * @return iterable<mixed>";
                    } else {
                        $docCommentText .= "\n * $child->name $child->value";
                    }
                }

                if ($child instanceof AttributeAwarePhpDocTextNode) {
                    $docCommentText .= "\n *" . ($child->text ? ' ' . $child->text : '');
                }
            }
        }

        $docCommentText .= "\n */";
        $classMethod->setDocComment(new Doc($docCommentText));
        $docComment = $this->phpDocInfoFactory->createFromNode($classMethod);
        $classMethod->setAttribute(AttributeKey::PHP_DOC_INFO, $docComment);
    }
}