<?php

namespace spec\Bounce\Emitter\Event;

use EventIO\InterOp\EventInterface;
use PhpSpec\ObjectBehavior;

class NamedSpec extends ObjectBehavior
{
    function it_is_an_event()
    {
        $this->shouldHaveType(EventInterface::class);
    }

    function it_returns_the_name_it_was_created_with()
    {
        $eventName = 'bip.bop.boo';
        $this->beConstructedThroughCreate($eventName);
        $this->name()->shouldReturn($eventName);
    }

    function it_acts_as_a_string()
    {
        $eventName = 'foo.bar.baz';
        $this->beConstructedThroughCreate($eventName);

        $this->__toString()->shouldReturn($eventName);
    }
}
