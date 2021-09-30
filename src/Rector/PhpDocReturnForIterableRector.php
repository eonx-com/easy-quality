<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use EonX\EasyQuality\Rector\ValueObject\PhpDocReturnForIterable;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

/**
 * @see \EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\PhpDocReturnForIterableRectorTest
 */
final class PhpDocReturnForIterableRector extends AbstractRector implements ConfigurableRectorInterface
{
    use PhpDocBlockTrait;

    /**
     * @var string
     */
    public const METHODS_TO_UPDATE = 'methods_to_update';

    /**
     * @var PhpDocReturnForIterable[]
     */
    private $methodsToUpdate;

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
     * @param ClassMethod $classMethod
     *
     * @throws \Rector\Core\Exception\ShouldNotHappenException
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

            if ($classMethod->returnType->name === 'iterable'
                && $this->isParentMethodHasDocBlock($classMethod) === false
            ) {
                $this->updateClassMethodPhpDocBlock($classMethod);
                $hasChanged = true;
            }
        }

        return $hasChanged ? $classMethod : null;
    }

    private function isParentMethodHasDocBlock(Node $classMethod): bool
    {
        $scope = $classMethod->getAttribute(AttributeKey::SCOPE);
        if (!$scope instanceof Scope) {
            // possibly trait
            return false;
        }

        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof ClassReflection) {
            return false;
        }

        /** @var string $methodName */
        $methodName = $this->getName($classMethod->name);

        foreach ($classReflection->getParents() as $parentClassReflection) {
            $nativeClassReflection = $parentClassReflection->getNativeReflection();
            // the class reflection aboves takes also @method annotations into an account
            if (!$nativeClassReflection->hasMethod($methodName)) {
                continue;
            }

            $parentReflectionMethod = $nativeClassReflection->getMethod($methodName);

            return $parentReflectionMethod->getDocComment() !== false;
        }

        return false;
    }
}
