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
            'multiline' => true,
        ];

        yield 'multi line array with indentLevel' => [
            'expectedOutput' => "[
        'test1' => 'test1',
        'test2' => 'test2',
    ]",
            'multiline' => true,
            'indentLevel' => 4,
        ];

        yield 'single line array' => [
            'expectedOutput' => "['test1' => 'test1', 'test2' => 'test2']",
            'multiline' => false,
        ];
    }

    #[DataProvider('providePrintData')]
    public function testPrintNodesSucceeds(string $expectedOutput, bool $multiline, ?int $indentLevel = null): void
    {
        $indentLevel ??= 0;
        $arrayItem1 = new ArrayItem(new String_('test1'), new String_('test1'));
        $arrayItem2 = new ArrayItem(new String_('test2'), new String_('test2'));
        if ($multiline) {
            $arrayItem1->setAttribute('multiLine', 'no-matter');
            $arrayItem2->setAttribute('multiLine', 'no-matter');
        }
        $array = new Array_([$arrayItem1, $arrayItem2], [
            'kind' => Array_::KIND_SHORT,
        ]);
        $printer = new Printer();
        // @phpstan-ignore method.dynamicName
        (fn ($method) => $this->{$method}())->call($printer, 'resetState');
        $printer->setStartIndentLevel($indentLevel);

        $result = $printer->printNodes([$array]);

        self::assertSame($expectedOutput, $result);
    }
}
