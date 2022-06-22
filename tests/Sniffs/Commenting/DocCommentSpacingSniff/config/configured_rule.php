<?php

declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Commenting\DocCommentSpacingSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(DocCommentSpacingSniff::class, [
        'linesCountBetweenAnnotationsGroups' => 1,
        'annotationsGroups' => [
            '@AppAssert*',
            '@Assert\\',
            '@param',
            '@return'
        ]
    ]);
};
