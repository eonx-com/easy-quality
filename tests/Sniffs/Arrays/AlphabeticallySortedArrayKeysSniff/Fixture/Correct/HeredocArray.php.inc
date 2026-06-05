<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Arrays\AlphabeticallySortedArrayKeysSniff\Fixture\Correct;

$config = [
    'apple' => <<<'SOMEHEREDOC'
        Nowdoc content here
            with nested indentation
        and a closing line.
        SOMEHEREDOC,
    'description' => <<<SOMEHEREDOC
        This is a plain heredoc.
            It keeps its indentation
        across multiple lines.
        SOMEHEREDOC,
    'zebra' => 'Last value',
];
