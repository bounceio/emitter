<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter\Acceptor;

use Bounce\Emitter\Collection\MappedListenerCollectionInterface;
use Bounce\Emitter\MappedListener\MappedListenerInterface;
use EventIO\InterOp\EventInterface;
use EventIO\InterOp\ListenerInterface;
use Generator;

/**
 * Class Acceptor.
 */
final class Acceptor implements AcceptorInterface
{
    /**
     * @var callable
     */
    private $middleware;

    /**
     * @var MappedListenerCollectionInterface
     */
    private $mappedListeners;

    public static function create(
        MappedListenerCollectionInterface $mappedListeners,
        callable $middleware
    ) {
        return new self($mappedListeners, $middleware);
    }

    /**
     * Acceptor constructor.
     *
     * @param \Bounce\Emitter\Collection\MappedListenerCollectionInterface $mappedListeners
     * @param callable                                                     $middleware
     */
    private function __construct(
        MappedListenerCollectionInterface $mappedListeners,
        callable $middleware
    ) {
        $this->middleware      = $middleware;
        $this->mappedListeners = $mappedListeners;
    }

    /**
     * @param \EventIO\InterOp\EventInterface $event
     *
     * @return iterable
     */
    public function __invoke(EventInterface $event): iterable
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
        $middleware = $this->middleware;

        return $middleware(
            $eventName,
            $listener,
            $priority
        );
    }
}
