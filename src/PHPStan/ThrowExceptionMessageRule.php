<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Throw_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Stmt\Throw_>
 */
final class ThrowExceptionMessageRule implements Rule
{
    public const ERROR_MESSAGE =
        'Exception message must be either a variable or a translation message, started with one of [%s]';

    private const DEFAULT_VALID_PREFIXES = ['exceptions.'];

    /**
     * @param string[] $validPrefixes
     */
    public function __construct(
        private readonly ?string $exceptionInterface = null,
        private readonly array $validPrefixes = self::DEFAULT_VALID_PREFIXES
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

        if ($this->exceptionInterface !== null
            && $expr->class instanceof Name
            && \is_a($expr->class->toString(), $this->exceptionInterface, true) === false
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
            $errors[] = \sprintf(
                self::ERROR_MESSAGE,
                \implode(', ', $this->validPrefixes)
            );
        }

        return $errors;
    }

    private function startsWithValidPrefix(string $message): bool
    {
        foreach ($this->validPrefixes as $validPrefix) {
            if (\str_starts_with($message, (string)$validPrefix)) {
                return true;
            }
        }

        return false;
    }
}
