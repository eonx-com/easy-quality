<?php

namespace EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Fixture;

use EonX\EasyQuality\Tests\Rector\ReturnArrayToYieldRector\Source\EventSubscriberInterface;

class SomeEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        /**
         * Some text.
         *
         * @see someFunction
         */
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
     *
     * Some text.
     *
     * @see someFunction
     */
    public static function getSubscribedEvents(): iterable
    {
        yield 'event' => 'callback';
    }
}

?>
