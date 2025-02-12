<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\MakeClassAbstractSniff;

use EonX\EasyQuality\Sniffs\Classes\AvoidPublicPropertiesSniff;
use EonX\EasyQuality\Sniffs\Classes\RepositorySniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class RepositorySniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritdoc
     */
    public static function provideFixtures(): iterable
    {
        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/Entity/OrmEntityAttributeRepositoryClassTestCase.php',
            'expectedErrors' => [
                [
                    'line' => 6,
                    'code' => RepositorySniff::class . '.OrmEntityAttributeRepositoryClassExists',
                ],
            ],
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/Repository/ExtendsNotAbstractRepositoryTestCase.php',
            'expectedErrors' => [
                [
                    'line' => 6,
                    'code' => RepositorySniff::class . '.ExtendsNotAbstractRepository',
                ],
            ],
        ];

        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/EntityGetRepositoryTestCase.php',
            'expectedErrors' => [
                [
                    'line' => 10,
                    'code' => RepositorySniff::class . '.EntityManagerGetRepository',
                ],
            ],
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
