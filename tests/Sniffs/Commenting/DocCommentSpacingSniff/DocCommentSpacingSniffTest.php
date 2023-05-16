<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Commenting\DocCommentSpacingSniff;

use EonX\EasyQuality\Tests\Sniffs\AbstractSniffTestCase;

final class DocCommentSpacingSniffTest extends AbstractSniffTestCase
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
        yield 'Correct' => [
            'filePath' => __DIR__ . '/Fixture/Correct/DocCommentSpacingSniffTest.php.inc',
        ];

        yield 'Wrong' => [
            'filePath' => __DIR__ . '/Fixture/Wrong/DocCommentSpacingSniffTest.php.inc',
        ];
    }
}
