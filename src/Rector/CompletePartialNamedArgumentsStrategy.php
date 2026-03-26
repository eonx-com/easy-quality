<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Rector;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Reflection\ClassReflection;
use SavinMikhail\AddNamedArgumentsRector\Config\ConfigStrategy;
use SavinMikhail\AddNamedArgumentsRector\Config\DefaultStrategy;

/**
 * Only converts calls to named arguments when at least one argument is already named.
 * Delegates all other checks (variadic, @no-named-arguments, etc.) to DefaultStrategy.
 */
final readonly class CompletePartialNamedArgumentsStrategy implements ConfigStrategy
{
    /**
     * @param \PHPStan\Reflection\ExtendedParameterReflection[] $parameters
     */
    public static function shouldApply(
        FuncCall|StaticCall|MethodCall|New_ $node,
        array $parameters,
        ?ClassReflection $classReflection = null,
    ): bool {
        $hasAtLeastOneNamedArg = array_any($node->args, fn($arg): bool => $arg instanceof Arg && $arg->name !== null);
        if ($hasAtLeastOneNamedArg === false) {
            return false;
        }

        return DefaultStrategy::shouldApply($node, $parameters, $classReflection);
    }
}
