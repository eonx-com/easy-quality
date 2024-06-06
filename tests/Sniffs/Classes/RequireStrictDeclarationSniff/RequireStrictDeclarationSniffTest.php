<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Classes\RequireStrictDeclarationSniff;

use EonX\EasyQuality\Sniffs\Classes\RequireStrictDeclarationSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class RequireStrictDeclarationSniffTest extends AbstractSniffTestCase
{
    /**
     * @inheritDoc
     */
    public static function provideFixtures(): iterable
    {
        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/RequireStrictDeclarationSniffTest.php.inc',
            'expectedErrors' => [
                [
                    'line' => 1,
                    'code' => RequireStrictDeclarationSniff::class . '.RequireStrictDeclaration',
                ],
            ],
        ];
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }
}
