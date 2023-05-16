<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Commenting\AnnotationSortingSniff;

use EonX\EasyQuality\Sniffs\Commenting\AnnotationSortingSniff;
use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class AnnotationSortingSniffTest extends AbstractSniffTestCase
{
    public function provideConfig(): string
    {
        return __DIR__ . '/config/ecs.php';
    }

    /**
     * @inheritDoc
     */
    public function provideFixtures(): iterable
    {
        yield [
            'filePath' => __DIR__ . '/Fixture/Wrong/AnnotationSortingSniffTest.php.inc',
            'expectedErrors' => [
                [
                    'line' => 8,
                    'code' => AnnotationSortingSniff::class . '.AnnotationSortAlphabetically',
                ],
            ],
        ];
    }
}
