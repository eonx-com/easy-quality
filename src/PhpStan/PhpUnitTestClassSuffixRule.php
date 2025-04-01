<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Stmt\Class_>
 */
final readonly class PhpUnitTestClassSuffixRule implements Rule
{
    public function __construct(private ReflectionProvider $reflectionProvider)
    {
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->name === null) {
            return [];
        }

        $className = $node->name->toString();

        if ($scope->getNamespace() !== null) {
            $className = $scope->getNamespace() . '\\' . $className;
        }

        $reflection = $this->reflectionProvider->getClass($className);

        if ($reflection->is(TestCase::class) === false) {
            return [];
        }

        if ($reflection->isAbstract() && \preg_match('/TestCase$/', $className) !== 1) {
            return [
                RuleErrorBuilder::message('PHPUnit test case class must have a `TestCase` suffix.')
                    ->identifier('easyQuality.phpUnitTestClassSuffix')
                    ->build(),
            ];
        }

        if ($reflection->isAbstract() === false && \preg_match('/Test$/', $className) !== 1) {
            return [
                RuleErrorBuilder::message('PHPUnit test class must have a `Test` suffix.')
                    ->identifier('easyQuality.phpUnitTestClassSuffix')
                    ->build(),
            ];
        }

        return [];
    }
}
