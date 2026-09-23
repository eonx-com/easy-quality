<?php
declare(strict_types=1);

function decide(int $value): string
{
    if ($value > 0) {
        return 'positive';
    } elseif ($value < 0) {
        return 'negative';
    } else {
        return 'zero';
    }
}
