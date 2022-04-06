<?php

use DateTime;
use DateTimeImmutable;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;

class MyClassWithDoctrineColumnAttributes
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    protected Carbon $property1;

    #[Groups(groups: ['some-groups'])]
    #[ORM\Column(type: "date_immutable", length: 255)]
    private DateTimeImmutable $property2;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, length: 255)]
    protected CarbonImmutable $property3;

    #[ORM\Column(
        type: "datetime_immutable",
        length: 255
    )]
    protected CarbonImmutable $property4;

}
