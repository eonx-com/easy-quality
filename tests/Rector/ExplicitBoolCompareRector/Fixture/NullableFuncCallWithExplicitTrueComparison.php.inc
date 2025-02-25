<?php
function some_test_function(): ?bool
{
    return true;
}

if (some_test_function() === true) {
    echo 'true';
}
?>
