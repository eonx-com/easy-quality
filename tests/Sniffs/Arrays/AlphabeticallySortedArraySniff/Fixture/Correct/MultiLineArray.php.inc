<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Arrays\AlphabeticallySortedArraySniff\Fixture\Correct;

$array = [
    'a' => 1,
    'b' => 3,
    'z' => 0,
];

$array2 = array(
    'a' => 1,
    'b' => 3,
    'z' => 0,
);

$array3 = [
    'a' => 1,
    'b' => 3,
    'h',
    'z',
    'z' => 0,
];
