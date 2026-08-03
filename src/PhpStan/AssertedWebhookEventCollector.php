<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * Collects the `'event' => '...'` criteria asserted against the webhook entity, see WebhookEventCoverageRule.
 *
 * Both shapes are collected: `findOneEntity(Webhook::class, ['event' => 'merchant:update'])` and
 * `assertEntity(Webhook::class)->toBeInDb(['event' => 'merchant:update'])`. The criteria must belong to a
 * positive assertion, the entity class may sit anywhere in the chain.
 *
 * @implements \PHPStan\Collectors\Collector<\PhpParser\Node\Expr\MethodCall, string>
 */
final readonly class AssertedWebhookEventCollector implements Collector
{
    private const string EVENT_KEY = 'event';

    /**
     * @var string[]
     */
    private const array POSITIVE_ASSERTIONS = ['findOneEntity', 'toBeInDb'];

    /**
     * @param string $entityClass The asserted webhook entity, such as `App\Entity\Webhook`
     */
    public function __construct(private string $entityClass) {}

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): ?string
    {
        if (self::isPositiveAssertion($node) === false) {
            return null;
        }

        $event = self::getAssertedEvent($node->getArgs());

        if ($event === null) {
            return null;
        }

        // The entity class may sit in an earlier call of the chain, such as `assertEntity(Webhook::class)`
        return $this->assertsEntityClass(self::getChainArguments($node), $scope) ? $event : null;
    }

    /**
     * @param \PhpParser\Node\Arg[] $arguments
     */
    private static function getAssertedEvent(array $arguments): ?string
    {
        foreach ($arguments as $argument) {
            if ($argument->value instanceof Array_ === false) {
                continue;
            }

            foreach ($argument->value->items as $item) {
                if ($item->key instanceof String_
                    && $item->key->value === self::EVENT_KEY
                    && $item->value instanceof String_) {
                    return $item->value->value;
                }
            }
        }

        return null;
    }

    /**
     * @return \PhpParser\Node\Arg[]
     */
    private static function getChainArguments(MethodCall $node): array
    {
        $arguments = [];

        for ($call = $node; $call instanceof MethodCall; $call = $call->var) {
            $arguments = [...$arguments, ...$call->getArgs()];
        }

        return $arguments;
    }

    private static function isPositiveAssertion(MethodCall $call): bool
    {
        return $call->name instanceof Identifier
            && \in_array($call->name->toString(), self::POSITIVE_ASSERTIONS, true);
    }

    /**
     * @param \PhpParser\Node\Arg[] $arguments
     */
    private function assertsEntityClass(array $arguments, Scope $scope): bool
    {
        $entityClass = \ltrim($this->entityClass, '\\');

        foreach ($arguments as $argument) {
            // A `Webhook::class` argument resolves to one constant string, the class name
            foreach ($scope->getType($argument->value)->getConstantStrings() as $constantString) {
                if (\ltrim($constantString->getValue(), '\\') === $entityClass) {
                    return true;
                }
            }
        }

        return false;
    }
}
