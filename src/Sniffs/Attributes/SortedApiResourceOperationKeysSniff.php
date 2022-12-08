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
use PhpParser\ParserFactory;

final class SortedApiResourceOperationKeysSniff implements Sniff
{
    /**
     * @var string
     */
    private const API_RESOURCE_OPERATIONS_NOT_SORTED = 'ApiResourceOperationsNotSorted';

    /**
     * @var array<string>
     */
    private const API_RESOURCE_OPERATIONS_TO_PROCESS = ['collectionOperations', 'itemOperations'];

    /**
     * @var string
     */
    private const FILE_PARSE_ERROR = 'FileParseError';

    /**
     * @var mixed[]
     */
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

            if (
                $token['code'] === \T_PARAM_NAME
                && \in_array($token['content'], self::API_RESOURCE_OPERATIONS_TO_PROCESS, true)
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

    /**
     * @return mixed[]
     */
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
                $arrayItem->value->items = $this->fixMultiLineOutput(
                    $subItems,
                    \intval($arrayItem->value->getAttribute('startLine'))
                );
                $items[$index] = $arrayItem;
            }

            if ($arrayItem->value instanceof MethodCall) {
                /** @var \PhpParser\Node\Expr\MethodCall $value */
                $value = $arrayItem->value;
                foreach ($value->args as $argIndex => $argument) {
                    if ($argument instanceof Arg && $argument->value instanceof Array_) {
                        /** @var \PhpParser\Node\Expr\ArrayItem[] $subItems */
                        $subItems = $argument->value->items;
                        $argument->value->items = $this->fixMultiLineOutput(
                            $subItems,
                            \intval($argument->value->getAttribute('startLine'))
                        );
                        $value->args[$argIndex] = $argument;
                    }
                }

                $items[$index] = $arrayItem;
            }

            $nextLine = \intval($arrayItem->getAttribute('startLine'));
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

    /**
     * @return array<bool|string>
     */
    private function getRanks(string $name): array
    {
        return [
            \str_starts_with($name, 'export') === false,
            \str_starts_with($name, 'get') === false,
            \str_starts_with($name, 'post') === false,
            \str_starts_with($name, 'put') === false,
            \str_starts_with($name, 'patch') === false,
            \str_starts_with($name, 'delete') === false,
            \str_starts_with($name, 'activate') === false,
            \str_starts_with($name, 'deactivate') === false,
            $name,
        ];
    }

    /**
     * @param \PhpParser\Node\Expr\ArrayItem[] $items
     *
     * @return \PhpParser\Node\Expr\ArrayItem[]
     */
    private function getSortedItems(array $items): array
    {
        if ($this->isNotAssociativeOnly($items) === false) {
            \uasort($items, function (ArrayItem $firstItem, ArrayItem $secondItem): int {
                $firstName = $this->getArrayKeyAsString($firstItem);
                $secondName = $this->getArrayKeyAsString($secondItem);

                return $this->getRanks($firstName ?? '') <=> $this->getRanks($secondName ?? '');
            });
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

        $phpcsFile->addErrorOnLine(
            'Api Operations should be sorted',
            $token['line'],
            self::API_RESOURCE_OPERATIONS_NOT_SORTED
        );

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

        $node->items = $this->fixMultiLineOutput($items, \intval($node->getAttribute('startLine')));

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
