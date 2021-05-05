<?php

class MissingDefaultValue
{
    public function __construct(?string $value)
    {
        $result = $value;
    }
}

?>

-----
<?php

class MissingDefaultValue
{
    public function __construct(?string $value = null)
    {
        $result = $value;
    }
}

?>
