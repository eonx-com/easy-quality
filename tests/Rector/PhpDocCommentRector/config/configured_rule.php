<?php

declare(strict_types=1);

use EonX\EasyQuality\Rector\PhpDocCommentRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(PhpDocCommentRector::class);
};
