<?php
declare(strict_types=1);

trait SomeTrait
{
    protected function protectedCall2()
    {
    }
    protected function protectedCall1()
    {
    }
    public function publicCall2()
    {
    }
    public function publicCall1()
    {
    }
}

?>

-----
<?php
declare(strict_types=1);

trait SomeTrait
{
    public function publicCall1()
    {
    }
    public function publicCall2()
    {
    }
    protected function protectedCall1()
    {
    }
    protected function protectedCall2()
    {
    }
}

?>
