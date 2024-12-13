<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Sniffs\Attributes;

use EonX\EasyQuality\Output\Printer;
use Error;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use PhpParser\ParserFactory;

/**
 * The sniff checks 'operations' attributes within the 'ApiResource' annotation are properly sorted.
 *
 * The sorting criteria are defined in the getRanks method.
 * If the keys are not sorted properly, the sniff will provide an option to fix the order.
 */
final class SortedApiResourceOperationsSniff implements Sniff
{
    /**
     * @var string
     */
    private const API_RESOURCE_OPERATIONS_NOT_SORTED = 'ApiResourceOperationsNotSorted';

    /**
     * @var array<string>
     */
    private const API_RESOURCE_OPERATIONS_TO_PROCESS = ['operations'];

    /**
     * @var string
     */
    private const FILE_PARSE_ERROR = 'FileParseError';

    private static array $parsedLine = [];

    private bool $isChanged = false;

    private Printer $prettyPrinter;

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr + 1]['content'] !== 'ApiResource') {
            return;
        }

        for ($i = $stackPtr + 1; $i <= $tokens[$stackPtr]['attribute_closer']; $i++) {
            $token = $tokens[$i];

            if ($token['code'] === \T_PARAM_NAME &&
                \in_array($token['content'], self::API_RESOURCE_OPERATIONS_TO_PROCESS, true) === true
            ) {
                $arrayContentOpenPtr = $phpcsFile->findNext(\T_OPEN_SHORT_ARRAY, $i + 1);

                if ($arrayContentOpenPtr === false) {
                    return;
                }

                $arrayContentClosePtr = $tokens[$arrayContentOpenPtr]['bracket_closer'];
                $this->processArrayContent($phpcsFile, $arrayContentOpenPtr, $arrayContentClosePtr);
            }
        }
    }

    public function register(): array
    {
        return [\T_ATTRIBUTE];
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

            if ($arrayItem->value instanceof New_) {
                /** @var \PhpParser\Node\Expr\New_ $value */
                $value = $arrayItem->value;
                $argValueMultiLine = $value->getAttribute('startLine') !== $value->getAttribute('endLine');
                foreach ($value->args as $argIndex => $argument) {
                    if ($argument instanceof Arg) {
                        if ($argValueMultiLine === true) {
                            $argument->setAttribute('multiLine', true);
                            $value->args[$argIndex] = $argument;
                        }
                    }
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

    private function getArrayKeyAsString(ArrayItem $node): ?string
    {
        $key = $node->key;

        if ($key === null) {
            return null;
        }

        $nodeKeyName = $this->prettyPrinter->prettyPrint([$key]);

        return \strtolower(\trim($nodeKeyName, " \t\n\r\0\x0B\"'"));
    }

    private function getRanks(ArrayItem $arrayItem): array
    {
        /** @var \PhpParser\Node\Expr\New_ $value */
        $value = $arrayItem->value;
        /** @var \PhpParser\Node\Name $class */
        $class = $value->class;
        $operationClass = $class->getParts()[0] ?? '';
        $hasUriTemplateArg = $this->hasUriTemplateArg($arrayItem);

        return [
            $operationClass === 'Get' && $hasUriTemplateArg === false,
            $operationClass === 'GetCollection' && $hasUriTemplateArg === false,
            $operationClass === 'Post' && $hasUriTemplateArg === false,
            $operationClass === 'Put' && $hasUriTemplateArg === false,
            $operationClass === 'Patch' && $hasUriTemplateArg === false,
            $operationClass === 'Delete' && $hasUriTemplateArg === false,
            $operationClass === 'Get' && $hasUriTemplateArg === true,
            $operationClass === 'GetCollection' && $hasUriTemplateArg === true,
            $operationClass === 'Post' && $hasUriTemplateArg === true,
            $operationClass === 'Put' && $hasUriTemplateArg === true,
            $operationClass === 'Patch' && $hasUriTemplateArg === true,
            $operationClass === 'Delete' && $hasUriTemplateArg === true,
            $operationClass,
        ];
    }

    /**
     * @param \PhpParser\Node\Expr\ArrayItem[] $items
     *
     * @return \PhpParser\Node\Expr\ArrayItem[]
     */
    private function getSortedItems(array $items): array
    {
        if ($this->isNotAssociativeOperationsArrayOnly($items)) {
            \uasort($items, fn (ArrayItem $firstItem, ArrayItem $secondItem): int =>
                $this->getRanks($secondItem) <=> $this->getRanks($firstItem));
        }

        return $items;
    }

    private function hasUriTemplateArg(ArrayItem $arrayItem): bool
    {
        /** @var \PhpParser\Node\Expr\New_ $value */
        $value = $arrayItem->value;
        /** @var \PhpParser\Node\Arg $arg */
        foreach ($value->args as $arg) {
            if ($arg->name instanceof Identifier && $arg->name->name === 'uriTemplate') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \PhpParser\Node\Expr\ArrayItem[] $items
     */
    private function isNotAssociativeOperationsArrayOnly(array $items): bool
    {
        $isNotAssociative = 1;

        foreach ($items as $arrayItem) {
            $isNotAssociative &= $arrayItem->key === null && $arrayItem->value instanceof New_;
        }

        return (bool)$isNotAssociative;
    }

    private function processArrayContent(File $phpcsFile, int $bracketOpenerPointer, int $bracketCloserPointer): void
    {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$bracketOpenerPointer];
        $code = $phpcsFile->getTokensAsString($bracketOpenerPointer, $bracketCloserPointer - $bracketOpenerPointer + 1);

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

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
        /** @var \PhpParser\Node\Expr\Array_ $array */
        $array = $stmtExpr->expr;

        if ($array->items === null || \count($array->items) <= 1) {
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
            'Api Operations should be sorted',
            $bracketOpenerPointer,
            self::API_RESOURCE_OPERATIONS_NOT_SORTED
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
}
