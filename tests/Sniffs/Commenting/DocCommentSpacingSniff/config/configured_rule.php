<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Commenting\DocCommentSpacingSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(DocCommentSpacingSniff::class, [
        'annotationsGroups' => [
            '@AppAssert*',
            '@Assert\\',
            '@param',
            '@return',
        ],
        'linesCountBetweenAnnotationsGroups' => 1,
    ]);
};
