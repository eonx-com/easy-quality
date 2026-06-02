<?php

$messages = [
    'zebra' => 'Last item',
    'apple' => "{count, plural,
        one {# apple}
        other {# apples}
    }",
    'mango' => '{gender, select,
        female {She}
        male {He}
        other {They}
    }',
];
?>
-----
<?php

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
?>
