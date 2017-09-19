<?php

namespace spec\Bounce\Emitter\MappedListener\Queue;

use Bounce\Emitter\Acceptor\AcceptorInterface;
use Bounce\Emitter\MappedListener\MappedListenerInterface;
use Bounce\Emitter\MappedListener\Queue\QueueInterface;
use PhpSpec\ObjectBehavior;

class PriorityQueueSpec extends ObjectBehavior
{
    function it_is_a_mapped_listener_queue()
    {
        $this->shouldHaveType(QueueInterface::class);
    }

    function it_returns_listeners_in_priority_order(
        MappedListenerInterface $first,
        MappedListenerInterface $second,
        MappedListenerInterface $third
    ) {
        $foo = function() {};
        $bar = function() {};
        $baz = function() {};

        $this->queue($third, $second, $first);
        $first->priority()->willReturn(AcceptorInterface::PRIORITY_CRITICAL);
        $second->priority()->willReturn(AcceptorInterface::PRIORITY_NORMAL);
        $third->priority()->willReturn(AcceptorInterface::PRIORITY_LOW);

        $first->listener()->willReturn($foo);
        $second->listener()->willReturn($bar);
        $third->listener()->willReturn($baz);

        $this->listeners()->shouldIterateAs([$foo, $bar, $baz]);
    }
}
