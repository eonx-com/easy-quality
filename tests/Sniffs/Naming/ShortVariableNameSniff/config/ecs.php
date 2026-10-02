<?php
declare(strict_types=1);

use EonX\EasyQuality\Sniffs\Naming\ShortVariableNameSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\EndFileNewlineSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(ShortVariableNameSniff::class, [
        'exceptions' => ['id'],
    ]);

    // Fixable, to force a second fixer loop over the same file (see FixerLoopStability fixture)
    $ecsConfig->rule(EndFileNewlineSniff::class);
};
