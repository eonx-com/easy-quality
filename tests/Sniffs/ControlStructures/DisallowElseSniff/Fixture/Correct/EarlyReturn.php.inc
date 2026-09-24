<?php
declare(strict_types=1);

function decide(int $value): string
{
    if ($value > 0) {
        return 'positive';
    }

    if ($value < 0) {
        return 'negative';
    }

    return 'zero';
}
