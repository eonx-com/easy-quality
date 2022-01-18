<?php
declare(strict_types=1);

trait SomeTrait
{
    protected $protectedProperty2;
    protected $protectedProperty1;
    public $publicProperty2;
    public $publicProperty1;
}

?>

-----
<?php
declare(strict_types=1);

trait SomeTrait
{
    public $publicProperty1;
    public $publicProperty2;
    protected $protectedProperty1;
    protected $protectedProperty2;
}

?>
