<?php

namespace spec\Bounce\Emitter\MappedListener\Collection;

use Bounce\Emitter\Acceptor\AcceptorInterface;
use Bounce\Emitter\Collection\MappedListenerCollectionInterface;
use Bounce\Emitter\MappedListener\MappedListenerInterface;
use EventIO\InterOp\EventInterface;
use PhpSpec\ObjectBehavior;

class MappedListenersSpec extends ObjectBehavior
{

    function it_is_a_mapped_listener_collection()
    {
        $this->beConstructedThroughCreate();
        $this->shouldHaveType(MappedListenerCollectionInterface::class);
    }

    function it_returns_listeners_for_an_event(
        MappedListenerInterface $mappedListener,
        EventInterface $event
    ) {
        $listener = function(){};

        $mappedListener->listener()->willReturn($listener);
        $mappedListener->matches($event)->willReturn(true);
        $mappedListener->priority()->willReturn(AcceptorInterface::PRIORITY_NORMAL);

        $this->beConstructedThroughCreate();
        $this->add($mappedListener);
        $this->listenersFor($event)->shouldIterateAs([$listener]);
    }

    function it_returns_listeners_in_the_correct_order(
        MappedListenerInterface $firstMappedListener,
        MappedListenerInterface $secondMappedListener,
        EventInterface $event
    ) {
        $firstListener  = function() {};
        $secondListener = function() {};

        $firstMappedListener->matches($event)->willReturn(true);
        $secondMappedListener->matches($event)->willReturn(true);

        $firstMappedListener->priority()->willReturn(AcceptorInterface::PRIORITY_HIGH);
        $secondMappedListener->priority()->willReturn(AcceptorInterface::PRIORITY_NORMAL);

        $firstMappedListener->listener()->willReturn($firstListener);
        $secondMappedListener->listener()->willReturn($secondListener);

        $this->beConstructedThroughCreate($secondMappedListener, $firstMappedListener);
        $this->listenersFor($event)->shouldIterateAs([$firstListener, $secondListener]);
    }
}
