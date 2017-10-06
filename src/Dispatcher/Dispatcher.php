<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter\Dispatcher;

use Bounce\Emitter\Acceptor\AcceptorInterface;
use Bounce\Emitter\DispatchLoop\DispatchLoop;
use Bounce\Emitter\DispatchLoop\DispatchLoop2;
use Bounce\Emitter\EventQueue\EventQueue;
use Bounce\Emitter\EventQueue\EventQueueInterface;
use Bounce\Emitter\Middleware\DispatcherMiddlewareInterface;
use EventIO\InterOp\EventInterface;
use stdClass;

/**
 * Class Dispatcher.
 */
final class Dispatcher implements DispatcherInterface
{
    /**
     * @var EventQueueInterface
     */
    private $queue;

    /**
     * @var DispatcherMiddlewareInterface
     */
    private $middleware;

    /**
     * @var DispatchLoop
     */
    private $currentLoop;

    /**
     * @param DispatcherMiddlewareInterface $dispatcherMiddleware
     * @param EventQueueInterface|null      $queue
     *
     * @return Dispatcher
     */
    public static function create(
        DispatcherMiddlewareInterface $dispatcherMiddleware,
        EventQueueInterface $queue = null
    ): self {
        if (!$queue) {
            $queue = EventQueue::create();
        }

        return new self($queue, $dispatcherMiddleware);
    }

    /**
     * Dispatcher constructor.
     *
     * @param EventQueueInterface           $queue
     * @param DispatcherMiddlewareInterface $dispatcherMiddleware
     */
    private function __construct(
        EventQueueInterface $queue,
        DispatcherMiddlewareInterface $dispatcherMiddleware
    ) {
        $this->queue = $queue;
        $this->middleware = $dispatcherMiddleware;
    }

    /**
     * @param EventInterface[] ...$events
     *
     * @return DispatcherInterface
     */
    public function enqueue(...$events): DispatcherInterface
    {
        $this->queueEvents($events);

        return $this;
    }

    /**
     * @return bool
     */
    public function isDispatching(): bool
    {
        if ($this->currentLoop) {
            return $this->currentLoop->isDispatching();
        }

        return false;
    }

    /**
     * @param AcceptorInterface $acceptor
     * @param iterable          $events
     *
     * @return DispatcherInterface
     */
    public function dispatch(
        AcceptorInterface $acceptor,
        iterable $events = []
    ): DispatcherInterface {
        $this->queueEvents($events);

        if (!$this->isDispatching()) {
            $this->dispatchQueue($acceptor);
        }

        return $this;
    }

    public function dispatchQueue(AcceptorInterface $acceptor)
    {
        foreach ($this->queue->events() as $event) {
            $this->dispatchEvent($event, $acceptor);
        }
    }

    /**
     * @param mixed             $event    The event to dispatch through listeners
     * @param AcceptorInterface $acceptor
     */
    private function dispatchEvent(
        $event,
        AcceptorInterface $acceptor
    ) {
        $this->currentLoop = $this->createDispatchLoop(
            $event,
            $acceptor
        );
        $this->currentLoop->dispatch();
    }

    /**
     * @param iterable $events
     */
    private function queueEvents(iterable $events)
    {
        $this->queue->queueEvents($events);
    }

    /**
     * @param $event
     * @param $acceptor
     *
     * @return mixed
     */
    private function createDispatchLoop($event, $acceptor): DispatchLoop
    {
        $dispatchLoop = new DispatchLoop2(
            $event,
            $acceptor
        );

        return $this->middleware->dispatch($dispatchLoop);
    }
}
