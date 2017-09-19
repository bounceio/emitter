<?php

namespace spec\Bounce\Emitter\EventQueue;

use EventIO\InterOp\EventInterface;
use Generator;
use PhpSpec\ObjectBehavior;

class EventQueueSpec extends ObjectBehavior
{
    function it_returns_an_iterator_of_events(
        EventInterface $event
    ) {
        $this->beConstructedThroughCreate();
        $this->queueEvent($event);
        $this->events()->shouldBeAnInstanceOf(Generator::class);
        $this->events()->shouldIterateAs([$event]);
    }

    function it_allows_queueing_multiple_events(
        EventInterface $event1,
        EventInterface $event2,
        EventInterface $event3
    ) {
        $this->beConstructedThroughCreate();
        $this->queueEvents([$event1, $event2])
            ->events()->shouldIterateAs([$event1, $event2]);
        $this->queueEvent($event3);
        $this->events()->shouldIterateAs([$event3]);
    }
}
