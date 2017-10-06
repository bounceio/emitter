<?php

namespace Bounce\Emitter\DispatchLoop;

use EventIO\InterOp\EventInterface;
use Generator;

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 20/09/2017
 * Time: 08:14
 */
class DispatchLoop2 implements DispatchLoopInterface
{
    /**
     * @var callable
     */
    public $event;

    /**
     * @var callable
     */
    public $acceptor;

    private $dispatching = false;

    public static function create($event, $acceptor)
    {
        if (!is_callable($event)) {
            $event = function() use ($event) {
                return $event;
            };
        }

        return new self($event, $acceptor);
    }

    /**
     * DispatchLoop2 constructor.
     *
     * @param callable|\EventIO\InterOp\EventInterface $event
     * @param callable                                 $acceptor
     */
    public function __construct(callable $event, callable $acceptor)
    {
        $this->event    = $event;
        $this->acceptor = $acceptor;
    }


    /**
     * @return \Bounce\Emitter\DispatchLoop\DispatchLoop2
     */
    public function __invoke()
    {
        return $this->dispatch();
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
        $event     = $this->event();
        $listeners = $this->listeners($event);

        while ($this->continueDispatching($event, $listeners)) {
            $listeners->current()->handle($event);
            $listeners->next();
        }
    }

    /**
     * @param \EventIO\InterOp\EventInterface $event
     * @param \Generator                      $listeners
     *
     * @return bool
     */
    private function continueDispatching(
        EventInterface $event,
        Generator $listeners
    ) {
        return (!$event->isPropagationStopped()) && $listeners->valid();
    }

    private function event(): EventInterface
    {
        $event = $this->event;

        return $event;
    }

    /**
     * @return Generator
     */
    private function listeners($event): Generator
    {
        $acceptor = $this->acceptor;

        yield from $acceptor($event);
    }

    /**
     * @return bool
     */
    public function isDispatching(): bool
    {
        return $this->dispatching;
    }
}
