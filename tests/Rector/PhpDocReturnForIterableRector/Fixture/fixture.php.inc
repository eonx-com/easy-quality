<?php

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\EventSubscriberInterface;

class SomeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): iterable
    {
        yield 'event' => 'callback';
    }
}

?>
-----
<?php

namespace EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Fixture;

use EonX\EasyQuality\Tests\Rector\PhpDocReturnForIterableRector\Source\EventSubscriberInterface;

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
