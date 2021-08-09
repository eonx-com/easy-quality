<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Source\EventSubscriberInterface;

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

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayClassMethodToYieldRector\Source\EventSubscriberInterface;

class SomeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return mixed[]
     */
    public static function getSubscribedEvents(): iterable
    {
        yield 'event' => 'callback';
    }
}

?>
