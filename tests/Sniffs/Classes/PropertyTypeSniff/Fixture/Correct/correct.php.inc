<?php

class MyClassWithDateTimeProperty
{
    private DateTimeImmutable $property1;

    protected CarbonImmutable $property2;

    public ?DateTimeImmutable $property3;

    private ?\Carbon\CarbonImmutable $property4;
}
