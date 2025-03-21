<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Throw_;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Expr\Throw_>
 */
final readonly class ThrowExceptionMessageRule implements Rule
{
    private const DEFAULT_VALID_PREFIXES = ['exceptions.'];

    private const ERROR_MESSAGE =
        'Exception message must be either a variable or a translation message, started with one of [%s]';

    /**
     * @param string[] $validPrefixes
     */
    public function __construct(
        private string $exceptionInterface,
        private ReflectionProvider $reflectionProvider,
        private array $validPrefixes = self::DEFAULT_VALID_PREFIXES,
    ) {
    }

    public function getNodeType(): string
    {
        return Throw_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $expr = $node->expr;

        if ($expr instanceof New_ === false) {
            return [];
        }

        if (
            $expr->class instanceof Name
            && $expr->class->toString() !== $this->exceptionInterface
            && $this->reflectionProvider
                ->getClass($expr->class->toString())
                ->isSubclassOf($this->exceptionInterface) === false
        ) {
            return [];
        }

        if (\count($expr->args) === 0) {
            return [];
        }

        $firstArg = $expr->args[0];

        if ($firstArg instanceof Arg === false) {
            return [];
        }

        $stringNode = $firstArg->value;
        if ($stringNode instanceof String_ === false) {
            return [];
        }

        $errors = [];
        if ($this->startsWithValidPrefix($stringNode->value) === false) {
            $errors[] = RuleErrorBuilder::message(\sprintf(
                self::ERROR_MESSAGE,
                \implode(', ', $this->validPrefixes)
            ))
                ->identifier('easyQuality.exceptionMessage')
                ->build();
        }

        return $errors;
    }

    private function startsWithValidPrefix(string $message): bool
    {
        foreach ($this->validPrefixes as $validPrefix) {
            if (\str_starts_with($message, $validPrefix)) {
                return true;
            }
        }

        return false;
    }
}
