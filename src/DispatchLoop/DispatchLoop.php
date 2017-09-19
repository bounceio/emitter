<?php

namespace Bounce\Emitter\DispatchLoop;

use EventIO\InterOp\EventInterface;
use EventIO\InterOp\ListenerInterface;
use Generator;

/**
 * Class DispatchLoop.
 */
class DispatchLoop
{
    /**
     * @var EventInterface
     */
    private $event;

    /**
     * @var iterable
     */
    private $listeners;

    /**
     * @var bool
     */
    private $dispatching;

    /**
     * @param $dto
     *
     * @return DispatchLoop
     */
    public static function fromDto($dto): self
    {
        $acceptor = $dto->acceptor;

        return new self($dto->event, $acceptor);
    }

    /**
     * DispatchLoop constructor.
     *
     * @param EventInterface $event
     * @param iterable       $listeners
     */
    public function __construct(EventInterface $event, callable $listeners)
    {
        $this->event     = $event;
        $this->listeners = $listeners;
    }

    /**
     * @return bool
     */
    public function isDispatching()
    {
        return $this->dispatching;
    }

    /**
     * @return $this
     */
    public function dispatch()
    {
        $this->dispatching = true;

        $this->dispatchEventToListeners();
        $this->dispatching = false;

        return $this;
    }

    /**
     * Dispatch an event to the listeners.
     */
    private function dispatchEventToListeners()
    {
        $listeners = $this->listeners();

        while ($this->continueDispatching($listeners)) {
            $listener = $listeners->current();

            $this->dispatchListener($listener);
            $listeners->next();
        }
    }

    /**
     * @return Generator
     */
    private function listeners(): Generator
    {
        $listeners = $this->listeners;
        yield from $listeners($this->event);
    }

    /**
     * @param ListenerInterface $listener
     *
     * @return mixed
     */
    private function dispatchListener(ListenerInterface $listener)
    {
        $listener->handle($this->event);
    }

    private function continueDispatching(Generator $listeners)
    {
        return (!$this->event->isPropagationStopped()) && $listeners->valid();
    }
}
