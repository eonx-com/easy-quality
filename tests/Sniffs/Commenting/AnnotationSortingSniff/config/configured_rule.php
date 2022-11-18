<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Commenting\AnnotationSortingSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(AnnotationSortingSniff::class, [
        'alwaysTopAnnotations' => ['@param', '@return', '@throws']
    ]);
};
