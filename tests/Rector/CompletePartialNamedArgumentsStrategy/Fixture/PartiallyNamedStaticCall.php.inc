<?php

class StaticService
{
    public static function create(string $type, int $priority): self
    {
        return new self();
    }
}

StaticService::create('foo', priority: 10);

?>
-----
<?php

class StaticService
{
    public static function create(string $type, int $priority): self
    {
        return new self();
    }
}

StaticService::create(type: 'foo', priority: 10);

?>
