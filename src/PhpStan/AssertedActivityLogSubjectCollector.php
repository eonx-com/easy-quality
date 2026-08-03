<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Type\TypeCombinator;

/**
 * Collects the subjects asserted through `assertActivityLog()`, see ActivityLogSubjectCoverageRule.
 *
 * @implements \PHPStan\Collectors\Collector<\PhpParser\Node\Expr\MethodCall, string[]>
 */
final readonly class AssertedActivityLogSubjectCollector implements Collector
{
    public const string METHOD_NAME = 'assertActivityLog';

    private const string SUBJECT_ARGUMENT_NAME = 'subject';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall $node
     *
     * @return string[]|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if ($node->name instanceof Identifier === false || $node->name->toString() !== self::METHOD_NAME) {
            return null;
        }

        $subjectArgument = self::getSubjectArgument($node->getArgs());

        if ($subjectArgument === null) {
            return null;
        }

        // A nullable finder result resolves to no class name until `null` is removed
        $subjectType = TypeCombinator::removeNull($scope->getType($subjectArgument->value));
        $subjectClasses = $subjectType->getObjectClassNames();

        return $subjectClasses === [] ? null : $subjectClasses;
    }

    /**
     * The subject must be passed by name, positional calls are not supported.
     *
     * @param \PhpParser\Node\Arg[] $arguments
     */
    private static function getSubjectArgument(array $arguments): ?Arg
    {
        foreach ($arguments as $argument) {
            if ($argument->name?->toString() === self::SUBJECT_ARGUMENT_NAME) {
                return $argument;
            }
        }

        return null;
    }
}
