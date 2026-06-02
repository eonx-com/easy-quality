<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Arrays\AlphabeticallySortedArrayKeysSniff\Fixture\Correct;

$messages = [
    'apple' => "{count, plural,
        one {# apple}
        other {# apples}
    }",
    'mango' => '{gender, select,
        female {She}
        male {He}
        other {They}
    }',
    'zebra' => 'Last item',
];
