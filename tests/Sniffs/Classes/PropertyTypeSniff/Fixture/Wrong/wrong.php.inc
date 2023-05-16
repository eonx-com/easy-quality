<?php

class MyClassWithDateTimeProperty
{
    private DateTime $property1;

    protected Carbon $property2;

    private DateTimeImmutable $property3;

    protected CarbonImmutable $property4;

    public ?DateTime $property5;

    protected ?\DateTime $property6;

    private ?Carbon\Carbon $property7;

    public integer $property8;
}
-----
<?php

class MyClassWithDateTimeProperty
{
    private DateTimeImmutable $property1;

    protected CarbonImmutable $property2;

    private DateTimeImmutable $property3;

    protected CarbonImmutable $property4;

    public ?DateTimeImmutable $property5;

    protected ?DateTimeImmutable $property6;

    private ?CarbonImmutable $property7;

    public string $property8;
}
