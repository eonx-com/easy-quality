<?php
declare(strict_types=1);

class SomeClass
{
    protected $protectedProperty2;
    public static $publicStaticProperty1;
    protected static $protectedStaticProperty2;
    private $privateProperty1;
    protected static $protectedStaticProperty1;
    public static $publicStaticProperty2;
    public $publicProperty2;
    protected $protectedProperty1;
    public $publicProperty1;
    private $privateProperty2;
}

?>

-----
<?php
declare(strict_types=1);

class SomeClass
{
    public static $publicStaticProperty1;
    public static $publicStaticProperty2;
    public $publicProperty1;
    public $publicProperty2;
    protected static $protectedStaticProperty1;
    protected static $protectedStaticProperty2;
    protected $protectedProperty1;
    protected $protectedProperty2;
    private $privateProperty1;
    private $privateProperty2;
}

?>
