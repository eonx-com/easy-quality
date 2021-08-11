<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\EventSubscriberInterface;

class SomeEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return ['event' => 'callback'];
    }
}

?>
-----
<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\EventSubscriberInterface;

class SomeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return iterable<mixed>
     */
    public static function getSubscribedEvents(): iterable
    {
        yield 'event' => 'callback';
    }
}

?>
