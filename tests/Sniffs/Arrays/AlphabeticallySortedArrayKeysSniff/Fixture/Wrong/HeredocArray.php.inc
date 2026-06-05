<?php

$config = [
    'zebra' => 'Last value',
    'description' => <<<SOMEHEREDOC
        This is a plain heredoc.
            It keeps its indentation
        across multiple lines.
        SOMEHEREDOC,
    'apple' => <<<'SOMEHEREDOC'
        Nowdoc content here
            with nested indentation
        and a closing line.
        SOMEHEREDOC,
];
?>
-----
<?php

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
?>
