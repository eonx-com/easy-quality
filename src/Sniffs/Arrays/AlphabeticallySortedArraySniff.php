<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Arrays;

use EonX\EasyQuality\Output\Printer;
use Error;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\ParserFactory;
use SlevomatCodingStandard\Helpers\TokenHelper;

final class AlphabeticallySortedArraySniff implements Sniff
{
    /**
     * @var string
     */
    private const ARRAY_NOT_SORTED_ALPHABETICALLY = 'ArrayNotSortedAlphabetically';

    /**
     * @var string
     */
    private const FILE_PARSE_ERROR = 'FileParseError';

    /**
     * A list of patterns to be checked to skip the array.
     * Specify a token type (e.g. `T_FUNCTION` or `T_CLASS`) as a key
     * and an array of regex patterns as a value to skip an array in the
     * corresponding tokens (functions, classes).
     *
     * Example: `[T_FUNCTION => ['/^someFunction/']]`.
     *
     * @var array<int, string[]>
     */
    public array $skipPatterns = [];

    /**
     * @var array<string, array<array-key, array{finish: int, start: int}>>
     */
    private static array $parsedLine = [];

    private bool $isChanged = false;

    private Printer $prettyPrinter;

    /**
     * @param int $bracketOpenerPointer
     */
    public function process(File $phpcsFile, $bracketOpenerPointer): void
    {
        if ($this->shouldSkip($phpcsFile, $bracketOpenerPointer)) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$bracketOpenerPointer];
        $bracketCloserPointer = $token['bracket_closer'] ?? $token['parenthesis_closer'];

        if ($bracketCloserPointer === null) {
            return;
        }

        $code = $phpcsFile->getTokensAsString($bracketOpenerPointer, $bracketCloserPointer - $bracketOpenerPointer + 1);
        $parser = (new ParserFactory())->createForHostVersion();

        try {
            $ast = $parser->parse('<?php' . \PHP_EOL . $code . ';');
        } catch (Error $error) {
            $phpcsFile->addErrorOnLine(
                "Parse error: {$error->getMessage()}",
                $token['line'],
                self::FILE_PARSE_ERROR
            );

            return;
        }

        if ($ast === null) {
            $phpcsFile->addErrorOnLine(
                'Unknown error while parsing the code',
                $token['line'],
                self::FILE_PARSE_ERROR
            );

            return;
        }

        /** @var \PhpParser\Node\Stmt\Expression $stmtExpr */
        $stmtExpr = $ast[0];
        $array = $stmtExpr->expr;

        if ($array instanceof Array_ === false || \count($array->items) <= 1) {
            return;
        }

        if (isset(self::$parsedLine[$phpcsFile->getFilename()]) === false) {
            self::$parsedLine[$phpcsFile->getFilename()] = [];
        }

        self::$parsedLine[$phpcsFile->getFilename()][] = [
            'finish' => $tokens[$bracketCloserPointer]['line'],
            'start' => $token['line'],
        ];
        $this->prettyPrinter = new Printer();
        $refactoredArray = $this->refactor($array);

        if ($this->isChanged === false) {
            return;
        }

        $fix = $phpcsFile->addFixableError(
            'The array should be sorted alphabetically',
            $bracketOpenerPointer,
            self::ARRAY_NOT_SORTED_ALPHABETICALLY
        );

        if ($fix !== false) {
            $this->setStartIndent($phpcsFile, $bracketOpenerPointer);

            $newContent = $this->prettyPrinter->printNodes([$refactoredArray]);

            $phpcsFile->fixer->beginChangeset();

            for ($iterator = $bracketOpenerPointer; $iterator <= $bracketCloserPointer; $iterator++) {
                $phpcsFile->fixer->replaceToken($iterator, '');
            }
            $phpcsFile->fixer->replaceToken($bracketOpenerPointer, $newContent);

            $phpcsFile->fixer->endChangeset();
        }

        $this->isChanged = false;
    }

    public function register(): array
    {
        return [\T_ARRAY, \T_OPEN_SHORT_ARRAY];
    }

    /**
     * @param \PhpParser\Node\Expr\ArrayItem[] $items
     *
     * @return \PhpParser\Node\Expr\ArrayItem[]
     */
    private function fixMultiLineOutput(array $items, ?int $currentLine = null): array
    {
        $currentLine ??= 0;

        foreach ($items as $index => $arrayItem) {
            if ($arrayItem->value instanceof Array_) {
                /** @var \PhpParser\Node\Expr\ArrayItem[] $subItems */
                $subItems = $arrayItem->value->items;
                /** @var int $startLine */
                $startLine = $arrayItem->value->getAttribute('startLine');
                $arrayItem->value->items = $this->fixMultiLineOutput($subItems, $startLine);
                $items[$index] = $arrayItem;
            }

            if ($arrayItem->value instanceof MethodCall) {
                /** @var \PhpParser\Node\Expr\MethodCall $value */
                $value = $arrayItem->value;
                foreach ($value->args as $argIndex => $argument) {
                    if ($argument instanceof Arg && $argument->value instanceof Array_) {
                        /** @var \PhpParser\Node\Expr\ArrayItem[] $subItems */
                        $subItems = $argument->value->items;
                        /** @var int $startLine */
                        $startLine = $argument->value->getAttribute('startLine');
                        $argument->value->items = $this->fixMultiLineOutput($subItems, $startLine);
                        $value->args[$argIndex] = $argument;
                    }
                }

                $items[$index] = $arrayItem;
            }

            /** @var int $nextLine */
            $nextLine = $arrayItem->getAttribute('startLine');
            if ($nextLine !== $currentLine) {
                $arrayItem->setAttribute('multiLine', true);
                $currentLine = $nextLine;
            }

            $items[$index] = $arrayItem;
        }

        return $items;
    }

    private function getKeyForComparison(ArrayItem $node): string
    {
        $key = $node->key;

        if ($key === null) {
            $key = $node->value;
        }

        $result = $this->prettyPrinter->prettyPrint([$key]);

        return \strtolower((string)\preg_replace('/[^a-zA-Z0-9_\s]/', '', $result));
    }

    /**
     * @param \PhpParser\Node\Expr\ArrayItem[] $items
     *
     * @return \PhpParser\Node\Expr\ArrayItem[]
     */
    private function getSortedItems(array $items): array
    {
        foreach ($items as $index => $arrayItem) {
            if ($arrayItem->value instanceof Array_) {
                $arrayItem->value = $this->refactor($arrayItem->value);

                $items[$index] = $arrayItem;
            }

            if ($arrayItem->value instanceof MethodCall) {
                /** @var \PhpParser\Node\Expr\MethodCall $value */
                $value = $arrayItem->value;
                foreach ($value->args as $argIndex => $argument) {
                    if ($argument instanceof Arg && $argument->value instanceof Array_) {
                        $argument->value = $this->refactor($argument->value);

                        $value->args[$argIndex] = $argument;
                    }
                }

                $items[$index] = $arrayItem;
            }
        }

        if ($this->isNotAssociativeOnly($items) === false) {
            \uasort(
                $items,
                function (ArrayItem $firstItem, ArrayItem $secondItem): int {
                    $value1 = $this->getKeyForComparison($firstItem);
                    $value2 = $this->getKeyForComparison($secondItem);

                    if (\str_starts_with($value1, '_') && \str_starts_with($value2, '_')) {
                        return $value1 <=> $value2;
                    }

                    if (\str_starts_with($value1, '_')) {
                        return 1;
                    }

                    if (\str_starts_with($value2, '_')) {
                        return -1;
                    }

                    return $value1 <=> $value2;
                }
            );
        }

        return $items;
    }

    /**
     * @param \PhpParser\Node\Expr\ArrayItem[] $items
     */
    private function isNotAssociativeOnly(array $items): bool
    {
        $isNotAssociative = 1;

        foreach ($items as $arrayItem) {
            $isNotAssociative &= $arrayItem->key === null;
        }

        return (bool)$isNotAssociative;
    }

    private function refactor(Array_ $node): Array_
    {
        /** @var \PhpParser\Node\Expr\ArrayItem[] $items */
        $items = $node->items;

        if (\count($items) === 0) {
            return $node;
        }

        $items = $this->getSortedItems($items);

        if ($node->items !== $items) {
            $this->isChanged = true;
        }

        /** @var int $startLine */
        $startLine = $node->getAttribute('startLine');
        $node->items = $this->fixMultiLineOutput($items, $startLine);

        return $node;
    }

    private function setStartIndent(File $phpcsFile, int $bracketOpenerPointer): void
    {
        $token = $phpcsFile->getTokens()[$bracketOpenerPointer];
        $indentSize = 4;
        $indentLevel = (int)\floor(($token['column'] - 1) / $indentSize);
        $indentLevel *= $indentSize;

        $closePointer = $token['bracket_closer'] ?? $token['parenthesis_closer'];

        if ($closePointer === null) {
            $this->prettyPrinter->setStartIndentLevel($indentLevel);

            return;
        }

        $closeToken = $phpcsFile->getTokens()[$closePointer];

        if ($token['line'] === $closeToken['line']) {
            $this->prettyPrinter->setStartIndentLevel($indentLevel);

            return;
        }

        $indentLevel = (int)\floor(($closeToken['column'] - 1) / $indentSize);
        $indentLevel *= $indentSize;

        $this->prettyPrinter->setStartIndentLevel($indentLevel);
    }

    private function shouldSkip(File $phpcsFile, int $bracketOpenerPointer): bool
    {
        $tokens = $phpcsFile->getTokens();

        foreach ($this->skipPatterns as $tokenType => $patterns) {
            $sourcePointer = TokenHelper::findPrevious($phpcsFile, [$tokenType], $bracketOpenerPointer);

            if ($sourcePointer === null) {
                return false;
            }

            $namePointer = TokenHelper::findNextEffective($phpcsFile, $sourcePointer + 1, $bracketOpenerPointer);
            $name = $tokens[$namePointer]['content'];
            foreach ($patterns as $pattern) {
                if (\preg_match($pattern, $name) === 1) {
                    return true;
                }
            }
        }

        if (isset(self::$parsedLine[$phpcsFile->getFilename()])) {
            $tokens = $phpcsFile->getTokens();
            $token = $tokens[$bracketOpenerPointer];
            $bracketCloserPointer = $token['bracket_closer'] ?? $token['parenthesis_closer'];
            $startLine = $token['line'];
            $finishLine = $tokens[$bracketCloserPointer]['line'];

            foreach (self::$parsedLine[$phpcsFile->getFilename()] as $parsedLine) {
                if ($startLine >= $parsedLine['start'] && $finishLine <= $parsedLine['finish']) {
                    return true;
                }
            }
        }

        return false;
    }
}
