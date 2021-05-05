<?php
if (\in_array('value', []) === true) {
    $result = 'in array';
}
?>

-----
<?php
if (\in_array('value', [])) {
    $result = 'in array';
}
?>
