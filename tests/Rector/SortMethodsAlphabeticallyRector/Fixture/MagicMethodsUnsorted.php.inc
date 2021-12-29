<?php
declare(strict_types=1);

class SomeClass
{
    public function call1()
    {
    }
    public function __wakeup()
    {
    }
    public function __toString()
    {
    }
    public function __construct()
    {
    }
}

?>

-----
<?php
declare(strict_types=1);

class SomeClass
{
    public function __construct()
    {
    }
    public function __toString()
    {
    }
    public function __wakeup()
    {
    }
    public function call1()
    {
    }
}

?>
