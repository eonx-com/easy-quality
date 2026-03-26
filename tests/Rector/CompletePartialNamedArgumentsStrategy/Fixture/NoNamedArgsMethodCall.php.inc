<?php

class AnotherService
{
    public function process(string $name, int $count): void
    {
    }
}

$service = new AnotherService();
$service->process('test', 100);
