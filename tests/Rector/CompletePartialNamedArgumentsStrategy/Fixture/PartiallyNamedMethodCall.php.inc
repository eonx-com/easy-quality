<?php

class SomeService
{
    public function process(string $name, int $count, bool $flag): void
    {
    }
}

$service = new SomeService();
$service->process('test', count: 100, false);

?>
-----
<?php

class SomeService
{
    public function process(string $name, int $count, bool $flag): void
    {
    }
}

$service = new SomeService();
$service->process(name: 'test', count: 100, flag: false);

?>
