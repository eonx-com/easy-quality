<?php

#[FOo\Zzzzzz]
#[FoO\BarBaz2]
#[FOO\BarBaz1]
#[FooBar\Something]
class Whatever
{
}
-----
<?php

#[FooBar\Something]
#[FOO\BarBaz1]
#[FoO\BarBaz2]
#[FOo\Zzzzzz]
class Whatever
{
}
