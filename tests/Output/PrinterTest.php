<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Output;

use EonX\EasyQuality\Output\Printer;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Scalar\String_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Printer::class)]
final class PrinterTest extends TestCase
{
    /**
     * @see testPrintNodesSucceeds
     */
    public static function providePrintData(): iterable
    {
        yield 'multi line array' => [
            'expectedOutput' => "[
    'test1' => 'test1',
    'test2' => 'test2',
]",
            'itemAttributes' => [
                'multiLine' => 'no-matter',
            ],
            'indentLevel' => 0,
        ];

        yield 'multi line array with indentLevel' => [
            'expectedOutput' => "[
        'test1' => 'test1',
        'test2' => 'test2',
    ]",
            'itemAttributes' => [
                'multiLine' => 'no-matter',
            ],
            'indentLevel' => 4,
        ];

        yield 'single line array' => [
            'expectedOutput' => "['test1' => 'test1', 'test2' => 'test2']",
            'itemAttributes' => [],
            'indentLevel' => 0,
        ];
    }

    /**
     * @param array<string, string> $itemAttributes
     */
    #[DataProvider('providePrintData')]
    public function testPrintNodesSucceeds(string $expectedOutput, array $itemAttributes, int $indentLevel): void
    {
        $arrayItem1 = new ArrayItem(new String_('test1'), new String_('test1'), attributes: $itemAttributes);
        $arrayItem2 = new ArrayItem(new String_('test2'), new String_('test2'), attributes: $itemAttributes);
        $array = new Array_([$arrayItem1, $arrayItem2], [
            'kind' => Array_::KIND_SHORT,
        ]);
        $printer = new Printer();
        // @phpstan-ignore method.dynamicName
        (fn($method) => $this->{$method}())->call($printer, 'resetState');
        $printer->setStartIndentLevel($indentLevel);

        $result = $printer->printNodes([$array]);

        self::assertSame($expectedOutput, $result);
    }
}
