<?php

namespace spec\Bounce\Emitter\Dispatcher;

use ArrayIterator;
use Bounce\Emitter\Acceptor\AcceptorInterface;
use Bounce\Emitter\Dispatcher\DispatcherInterface;
use Bounce\Emitter\Event\Named;
use Bounce\Emitter\Listener\CallableListener;
use Bounce\Emitter\Map\Glob;
use Bounce\Emitter\Middleware\DispatcherMiddlewareInterface;
use EventIO\InterOp\EventInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DispatcherSpec extends ObjectBehavior
{
    function let(DispatcherMiddlewareInterface $middleware)
    {
        $this->beConstructedThroughCreate($middleware);
    }

    function it_is_a_dispatcher()
    {
        $this->shouldHaveType(DispatcherInterface::class);
    }

    function it_dispatches_queued_events(
        AcceptorInterface $acceptor
    ) {
        $event1 = Named::create('event1');
        $event2 = Named::create('event2');

        $this->enqueue($event1, $event2);
        $acceptor->listenersFor(Argument::type(EventInterface::class))->willReturn(new ArrayIterator());
        $this->dispatch($acceptor);
        $acceptor->listenersFor($event1)->shouldHaveBeenCalled();
        $acceptor->listenersFor($event2)->shouldHaveBeenCalled();
    }

    function it_does_not_propogate_stopped_events(
        AcceptorInterface $acceptor
    ) {
        $event = Named::create('event.foo');
        $event->stopPropagation();

        $this->enqueue($event);
        $this->dispatch($acceptor);
        $acceptor->listenersFor($event)->shouldNotHaveBeenCalled();

    }

    function it_checks_if_an_event_has_been_stopped(
        AcceptorInterface $acceptor
    ) {
        $event = Named::create('event.foo');
        $this->enqueue($event);

        $listener1 = new CallableListener(function(EventInterface $event) {
            $event->stopPropagation();
        });

        $listener2 = new CallableListener(function(EventInterface $event) {
            die('we should never get to this');
        });

        $acceptor->listenersFor($event)->willReturn([$listener1, $listener2]);

        $this->dispatch($acceptor);
    }
}
