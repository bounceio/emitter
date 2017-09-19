<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter;

use Bounce\Emitter\Acceptor\AcceptorInterface;
use Bounce\Emitter\Dispatcher\DispatcherInterface;
use EventIO\InterOp\EmitterInterface;
use EventIO\InterOp\EventInterface;
use EventIO\InterOp\ListenerAcceptorInterface;
use EventIO\InterOp\ListenerInterface;

/**
 * Class Emitter.
 */
final class Emitter implements EmitterInterface, ListenerAcceptorInterface
{
    /**
     * @var AcceptorInterface
     */
    private $acceptor;

    /**
     * @var DispatcherInterface
     */
    private $dispatcher;

    /**
     * Emitter constructor.
     *
     * @param AcceptorInterface   $acceptor
     * @param DispatcherInterface $dispatcher
     */
    public function __construct(
        AcceptorInterface $acceptor,
        DispatcherInterface $dispatcher
    ) {
        $this->acceptor     = $acceptor;
        $this->dispatcher   = $dispatcher;
    }

    /**
     * @param string                     $eventName The name of the event to listen for
     * @param callable|ListenerInterface $listener  A listener or callable
     * @param int                        $priority  Used to prioritise listeners for the same event
     *
     * @return mixed
     */
    public function addListener(
        $eventName,
        $listener,
        $priority = self::PRIORITY_NORMAL
    ) {
        return $this->addListeners(
            $eventName,
            [$listener],
            $priority
        );
    }

    /**
     * @param $eventName
     * @param iterable $listeners
     * @param int      $priority
     *
     * @return $this
     */
    public function addListeners(
        $eventName,
        iterable $listeners,
        $priority = self::PRIORITY_NORMAL
    ): self {
        $this->acceptor->addListeners($eventName, $listeners, $priority);

        return $this;
    }

    /**
     * @param EventInterface $event
     *
     * @return $this
     */
    public function emitEvent(EventInterface $event)
    {
        $this->emit($event);

        return $this;
    }

    /**
     * @param string $event
     *
     * @return $this
     */
    public function emitName($event)
    {
        $this->emit($event);

        return $this;
    }

    /**
     * @param array ...$events
     *
     * @return Emitter
     */
    public function emit(...$events): self
    {
        $this->emitBatch($events);

        return $this;
    }

    /**
     * @param iterable $events
     *
     * @return Emitter
     */
    public function emitBatch(iterable $events): self
    {
        $this->dispatcher->dispatch($this->acceptor, $events);

        return $this;
    }
}
