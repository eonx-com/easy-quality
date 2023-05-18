<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Classes\RequireStrictDeclarationSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(RequireStrictDeclarationSniff::class);
};
