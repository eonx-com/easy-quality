<?php
declare(strict_types=1);

namespace EonX\EasyQuality\PHPStan;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\Throw_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;

final class ThrowExceptionMessageRule implements Rule
{
    public const ERROR_MESSAGE = 'Exception message must be either a variable or a translation message, started with one of [%s]';

    /**
     * @var string
     */
    private $exceptionInterface;

    /**
     * @var string[]
     */
    private $validPrefixes;

    public function __construct(
        ?string $exceptionInterface = null,
        array $validPrefixes = ['exceptions.']
    ) {
        $this->validPrefixes = $validPrefixes;
        $this->exceptionInterface = $exceptionInterface;
    }


    public function getNodeType(): string
    {
        return Throw_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof Throw_) {
            throw new ShouldNotHappenException();
        }
        $expr = $node->expr;

        if (!$expr instanceof New_ && !$expr instanceof StaticCall) {
            return [];
        }

        if ($this->exceptionInterface !== null
            && !\is_a($expr->class->toString(), $this->exceptionInterface, true)
        ) {
            return [];
        }

        if (count($expr->args) === 0) {
            return [];
        }

        $stringNode = $expr->args[0]->value;
        if (!$stringNode instanceof Node\Scalar\String_) {
            return [];
        }
        $errors = [];
        if (!$this->startsWithValidPrefix($stringNode->value)) {
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
            if (Strings::startsWith($message, $validPrefix) === true) {
                return true;
            }
        }

        return false;
    }
}
