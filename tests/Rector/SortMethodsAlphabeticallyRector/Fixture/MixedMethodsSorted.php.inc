<?php
declare(strict_types=1);

class SomeClass
{
    abstract public function publicAbstractCall1();

    abstract public function publicAbstractCall2();

    public static function publicStaticCall1()
    {
    }

    public static function publicStaticCall2()
    {
    }

    public function publicCall1()
    {
    }

    public function publicCall2()
    {
    }

    abstract protected function protectedAbstractCall1();

    abstract protected function protectedAbstractCall2();

    protected static function protectedStaticCall1()
    {
    }

    protected static function protectedStaticCall2()
    {
    }

    protected function protectedCall1()
    {
    }

    protected function protectedCall2()
    {
    }

    final protected function protectedFinalCall1()
    {
    }

    final protected function protectedFinalCall2()
    {
    }

    private function privateCall1()
    {
    }

    private function privateCall2()
    {
    }
}

?>
