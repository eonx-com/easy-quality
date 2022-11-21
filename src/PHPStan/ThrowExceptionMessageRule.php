<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Throw_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;

final class ThrowExceptionMessageRule implements Rule
{
    public const ERROR_MESSAGE = 'Exception message must be either a variable or a translation message' .
    ', started with one of [%s]';

    private const DEFAULT_VALID_PREFIXES = ['exceptions.'];

    /**
     * @var string[]|null
     */
    private $validPrefixes;

    public function __construct(private readonly ?string $exceptionInterface = null, ?array $validPrefixes = null)
    {
        $this->validPrefixes = $validPrefixes ?? self::DEFAULT_VALID_PREFIXES;
    }

    public function getNodeType(): string
    {
        return Throw_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($node instanceof Throw_ === false) {
            throw new ShouldNotHappenException();
        }
        $expr = $node->expr;

        if ($expr instanceof New_ === false) {
            return [];
        }

        if ($this->exceptionInterface !== null
            && \is_a($expr->class->toString(), $this->exceptionInterface, true) === false
        ) {
            return [];
        }

        if (\count($expr->args) === 0) {
            return [];
        }

        $stringNode = $expr->args[0]->value;
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
