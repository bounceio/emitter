<?php

namespace spec\Bounce\Emitter\Acceptor;

use Bounce\Emitter\Acceptor\AcceptorInterface;
use Bounce\Emitter\Listener\CallableListener;
use Bounce\Emitter\Collection\MappedListenerCollectionInterface;
use Bounce\Emitter\MappedListener\MappedListenerInterface;
use EventIO\InterOp\EventInterface;
use PhpSpec\ObjectBehavior;
use SplQueue;

class AcceptorSpec extends ObjectBehavior
{
    function let(MappedListenerCollectionInterface $listenerCollection)
    {
        $this->beConstructedThroughCreate($listenerCollection);
    }

    function it_is_a_listener_acceptor()
    {
        $this->shouldHaveType(AcceptorInterface::class);
    }

    function it_returns_a_listener(
        EventInterface $event,
        MappedListenerInterface $mappedListener,
        MappedListenerCollectionInterface $listenerCollection
    ) {
        $eventName = 'foo';

        $callable = function() {};
        $queue = new SplQueue();
        $listener = new CallableListener($callable);
        $queue->enqueue($listener);

        $listenerCollection->add($mappedListener)->shouldBeCalled();

        $listenerCollection->listenersFor($event)->willReturn($queue);

        $this->addListener(
            $eventName,
            $callable
        );

        $this->listenersFor($event)->shouldIterateAs($queue);
    }
}
