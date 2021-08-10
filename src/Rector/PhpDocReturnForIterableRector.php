<?php

declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use EonX\EasyQuality\ValueObject\PhpDocReturnForIterable;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\ConfiguredCodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;
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
                $hasChanged = true;
            }
        }

        return $hasChanged ? $classMethod : null;
    }
}