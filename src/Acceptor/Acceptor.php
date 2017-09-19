<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter\Acceptor;

use Bounce\Emitter\Collection\MappedListenerCollectionInterface;
use Bounce\Emitter\Collection\MappedListeners;
use Bounce\Emitter\MappedListener\MappedListenerInterface;
use Bounce\Emitter\Middleware\AcceptorMiddlewareInterface;
use EventIO\InterOp\EventInterface;
use EventIO\InterOp\ListenerInterface;
use Generator;

/**
 * Class Acceptor.
 */
final class Acceptor implements AcceptorInterface
{
    /**
     * @var AcceptorMiddlewareInterface
     */
    private $middleware;

    /**
     * @var MappedListenerCollectionInterface
     */
    private $mappedListeners;

    public static function create(
        AcceptorMiddlewareInterface $middleware,
        MappedListenerCollectionInterface $mappedListeners
    ) {
        return new self($middleware, $mappedListeners);
    }

    /**
     * Acceptor constructor.
     *
     * @param AcceptorMiddlewareInterface       $middleware
     * @param MappedListenerCollectionInterface $mappedListeners
     */
    private function __construct(
        AcceptorMiddlewareInterface       $middleware,
        MappedListenerCollectionInterface $mappedListeners
    ) {
        $this->middleware      = $middleware;
        $this->mappedListeners = $mappedListeners;
    }

    public function __invoke(EventInterface $event)
    {
        return $this->listenersFor($event);
    }

    /**
     * @param EventInterface $event
     *
     * @return iterable
     */
    public function listenersFor(EventInterface $event): iterable
    {
        yield from $this->mappedListeners->listenersFor($event);
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
        return $this->addListeners($eventName, [$listener], $priority);
    }

    /**
     * @param $eventName
     * @param iterable $listeners
     * @param int      $priority
     *
     * @return Acceptor
     */
    public function addListeners(
        $eventName,
        iterable $listeners,
        $priority = self::PRIORITY_NORMAL
    ): Acceptor {
        $mappedListeners = $this->mapListeners(
            $eventName,
            $listeners,
            $priority
        );

        $this->mappedListeners->addListeners($mappedListeners);

        return $this;
    }

    /**
     * @param $eventName
     * @param iterable $listeners
     * @param int      $priority
     *
     * @return Generator
     */
    private function mapListeners(
        $eventName,
        iterable $listeners,
        $priority = self::PRIORITY_NORMAL
    ): Generator {
        foreach ($listeners as $listener) {
            yield $this->mapListener(
                $eventName,
                $listener,
                $priority
            );
        }
    }

    /**
     * @param $eventName
     * @param $listener
     * @param int $priority
     *
     * @return MappedListenerInterface
     */
    private function mapListener(
        $eventName,
        $listener,
        $priority = self::PRIORITY_NORMAL
    ): MappedListenerInterface {
        return $this->middleware->listenerAdd(
            $eventName,
            $listener,
            $priority
        );
    }
}
