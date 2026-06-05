<?php

$messages = [
    'zebra' => 'Last item',
    'merchant.added' => <<<'ICU'
        {authenticatedActorType, select,
            merchant_admin {{resellerTradingName} added {targetMerchantAdminName} to {merchantTradingName}}
            other {{actorResellerAdminName} added {targetMerchantAdminName} to {merchantTradingName}}
        }
        ICU,
    'apple.count' => <<<ICU
        {count, plural,
            one {# apple}
            other {# apples}
        }
        ICU,
];
?>
-----
<?php

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
?>
