<?php
declare(strict_types=1);

namespace EonX\EasyQuality\Tests\Sniffs\Arrays\AlphabeticallySortedArrayKeysSniff\Fixture\Correct;

$messages = [
    'apple.count' => <<<ICU
        {count, plural,
            one {# apple}
            other {# apples}
        }
        ICU,
    'merchant.added' => <<<'ICU'
        {authenticatedActorType, select,
            merchant_admin {{resellerTradingName} added {targetMerchantAdminName} to {merchantTradingName}}
            other {{actorResellerAdminName} added {targetMerchantAdminName} to {merchantTradingName}}
        }
        ICU,
    'zebra' => 'Last item',
];
