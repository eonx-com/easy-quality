<?php
if (!\in_array('value', [])) {
    $result = 'not in array';
}
?>

-----
<?php
if (\in_array('value', []) === false) {
    $result = 'not in array';
}
?>
