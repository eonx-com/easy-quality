<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Attributes\GetCollectionOrderSniff;

use EonX\EasyQuality\Sniffs\Attributes\GetCollectionOrderSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GetCollectionOrderSniff::class)]
final class GetCollectionOrderSniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritdoc
     */
    public static function provideFixtures(): iterable
    {
        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/TopLevelOrder.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/TopLevelOrderAfterOperations.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/PerOperationOrder.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/NoGetCollection.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/MixedOrder.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/SimpleEntity.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Correct/GetCollectionNoParenthesesWithTopLevelOrder.php.inc',
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/MissingOrder.php.inc',
            'expectedErrors' => [
                [
                    'line' => 3,
                    'code' => GetCollectionOrderSniff::class . '.GetCollectionOrderMissing',
                ],
            ],
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/PartialOrder.php.inc',
            'expectedErrors' => [
                [
                    'line' => 3,
                    'code' => GetCollectionOrderSniff::class . '.GetCollectionOrderMissing',
                ],
            ],
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/GetCollectionNoOrder.php.inc',
            'expectedErrors' => [
                [
                    'line' => 3,
                    'code' => GetCollectionOrderSniff::class . '.GetCollectionOrderMissing',
                ],
            ],
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/NoOperationsNoOrder.php.inc',
            'expectedErrors' => [
                [
                    'line' => 3,
                    'code' => GetCollectionOrderSniff::class . '.GetCollectionOrderMissing',
                ],
            ],
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/GetCollectionNoParenthesesNoOrder.php.inc',
            'expectedErrors' => [
                [
                    'line' => 3,
                    'code' => GetCollectionOrderSniff::class . '.GetCollectionOrderMissing',
                ],
            ],
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/NestedOrderInContext.php.inc',
            'expectedErrors' => [
                [
                    'line' => 3,
                    'code' => GetCollectionOrderSniff::class . '.GetCollectionOrderMissing',
                ],
            ],
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/NestedOrderInSecurityExpression.php.inc',
            'expectedErrors' => [
                [
                    'line' => 3,
                    'code' => GetCollectionOrderSniff::class . '.GetCollectionOrderMissing',
                ],
            ],
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/DeepNestedOrderNotForGetCollection.php.inc',
            'expectedErrors' => [
                [
                    'line' => 3,
                    'code' => GetCollectionOrderSniff::class . '.GetCollectionOrderMissing',
                ],
            ],
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
