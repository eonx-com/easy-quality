<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use EonX\EasyQuality\Rector\ValueObject\PhpDocReturnForIterable;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @deprecated It is no required anymore, because of ignoring the PHPStan errors
 */
final class PhpDocReturnForIterableRector extends AbstractRector implements ConfigurableRectorInterface
{
    use PhpDocBlockTrait;

    /**
     * @var string
     */
    public const METHODS_TO_UPDATE = 'methods_to_update';

    /**
     * @var \EonX\EasyQuality\Rector\ValueObject\PhpDocReturnForIterable[]
     */
    private iterable $methodsToUpdate;

    public function configure(array $configuration): void
    {
        $methodsToUpdate = $configuration[self::METHODS_TO_UPDATE] ?? [];
        Assert::allIsInstanceOf($methodsToUpdate, PhpDocReturnForIterable::class);
        $this->methodsToUpdate = $methodsToUpdate;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @throws \Symplify\RuleDocGenerator\Exception\PoorDocumentationException
     * @throws \Symplify\RuleDocGenerator\Exception\ShouldNotHappenException
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Turns @return to @return iterable<mixed> in specified classes and methods',
            [
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
            ]
        );
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $classMethod
     *
     * @throws \ReflectionException
     */
    public function refactor(Node $classMethod): ?Node
    {
        $hasChanged = false;

        foreach ($this->methodsToUpdate as $methodToUpdate) {
            if ($this->isObjectType($classMethod, $methodToUpdate->getObjectType()) === false) {
                continue;
            }

            if ($this->isName($classMethod, $methodToUpdate->getMethod()) === false) {
                continue;
            }

            if (
                $classMethod->returnType instanceof Identifier
                && $classMethod->returnType->name === 'iterable'
                && $this->hasDocBlockInParentMethod($classMethod) === false
            ) {
                $this->updateClassMethodPhpDocBlock($classMethod);
                $hasChanged = true;
            }
        }

        return $hasChanged ? $classMethod : null;
    }
}
