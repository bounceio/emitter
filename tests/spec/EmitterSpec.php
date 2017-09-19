<?php

namespace spec\Bounce\Emitter;

use ArrayIterator;
use Bounce\Emitter\Acceptor\AcceptorInterface;
use Bounce\Emitter\Dispatcher\DispatcherInterface;
use Bounce\Emitter\Listener\CallableListener;
use Bounce\Emitter\Middleware\Emitter\EmitterMiddlewareInterface;
use EventIO\InterOp\EmitterInterface;
use EventIO\InterOp\EventInterface;
use EventIO\InterOp\ListenerAcceptorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EmitterSpec extends ObjectBehavior
{
    function let(
        AcceptorInterface $acceptor,
        DispatcherInterface $dispatcher
    ) {
        $this->beConstructedWith($acceptor, $dispatcher);
    }

    function it_is_an_event_emitter()
    {
        $this->shouldHaveType(EmitterInterface::class);
    }

    function it_is_a_listener_acceptor()
    {
        $this->shouldHaveType(ListenerAcceptorInterface::class);
    }

    function it_dispatches_a_single_event(
        EventInterface $event,
        $acceptor,
        $middleware
    ) {
        $eventName = 'foo';
        $listener = new CallableListener(function(){});

        $middleware->queue($event)->willReturn($event);

        $acceptor->addListener($eventName, $listener, ListenerAcceptorInterface::PRIORITY_NORMAL)
            ->shouldBeCalled();
        $acceptor->listenersFor($event)->will(function() use($listener) {
           yield $listener;
        });

        $this->addListener($eventName, $listener)->emitEvent($event);
    }

    function it_queues_events(
        EventInterface $firstEvent,
        EventInterface $secondEvent,
        EventInterface $thirdEvent,
        $acceptor,
        $middleware
    ) {
        $listeners = new ArrayIterator([]);

        $middleware->queue(Argument::type(EventInterface::class))
            ->willReturn($firstEvent, $secondEvent, $thirdEvent);

        $acceptor->listenersFor($firstEvent)->willReturn($listeners)
            ->shouldBeCalled();

        $acceptor->listenersFor($secondEvent)->willReturn($listeners)
            ->shouldBeCalled();

        $acceptor->listenersFor($thirdEvent)->willReturn($listeners)
            ->shouldBeCalled();


        $this->emit($firstEvent, $secondEvent, $thirdEvent);
    }
}
