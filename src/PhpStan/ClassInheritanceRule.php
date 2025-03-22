<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Stmt\Class_>
 */
final readonly class ClassInheritanceRule implements Rule
{
    /**
     * @param array<string, array<string>> $patternRequirements
     */
    public function __construct(private ReflectionProvider $reflectionProvider, private array $patternRequirements)
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

        foreach ($this->patternRequirements as $pattern => $requiredParents) {
            if (\preg_match($pattern, $className) === 1) {
                $reflection = $this->reflectionProvider->getClass($className);

                foreach ($requiredParents as $requiredParent) {
                    if ($reflection->is($requiredParent)) {
                        return [];
                    }
                }

                return [
                    RuleErrorBuilder::message(\sprintf(
                        'Class %s must implement or extend one of: %s.',
                        $className,
                        \implode(', ', $requiredParents)
                    ))
                        ->identifier('easyQuality.classInheritance')
                        ->build(),
                ];
            }
        }

        return [];
    }
}
