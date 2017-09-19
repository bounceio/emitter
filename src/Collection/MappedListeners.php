<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter\Collection;

use Bounce\Emitter\ListenerQueue\DsListenerQueue;
use Bounce\Emitter\ListenerQueue\ListenerQueueInterface;
use Bounce\Emitter\MappedListener\MappedListenerInterface;
use Ds\Set;
use EventIO\InterOp\EventInterface;

/**
 * Class MappedListeners.
 */
class MappedListeners implements MappedListenerCollectionInterface
{
    /**
     * @var \Ds\Set
     */
    private $mappedListeners;

    /**
     * @var \Bounce\Emitter\ListenerQueue\ListenerQueueInterface
     */
    private $queue;

    private $filter;

    /**
     * @param \Bounce\Emitter\ListenerQueue\ListenerQueueInterface $queue
     * @param                                                      $filter
     *
     * @return \Bounce\Emitter\Collection\MappedListeners
     */
    public static function create(
        callable $filter,
        ListenerQueueInterface $queue = null
    ): self {
        if (!$queue) {
            $queue = new DsListenerQueue();
        }

        return new self($queue, $filter);
    }

    /**
     * MappedListeners constructor.
     *
     * @param \Bounce\Emitter\ListenerQueue\ListenerQueueInterface $queue
     * @param                                                      $filter
     */
    private function __construct(
        ListenerQueueInterface $queue,
        callable $filter
    ) {
        $this->mappedListeners = new Set();
        $this->filter          = $filter;
        $this->queue           = $queue;
    }

    /**
     * {@inheritdoc}
     */
    public function add(MappedListenerInterface ...$mappedListeners)
    {
        return $this->addListeners($mappedListeners);
    }

    /**
     * @param iterable $mappedListeners
     *
     * @return $this
     */
    public function addListeners(iterable $mappedListeners)
    {
        foreach ($mappedListeners as $mappedListener) {
            $this->mappedListeners->add($mappedListener);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function listenersFor(EventInterface $event): iterable
    {
        yield from $this->queueFor($event)->listeners();
    }

    /**
     * @param \EventIO\InterOp\EventInterface $event
     *
     * @return \Bounce\Emitter\ListenerQueue\ListenerQueueInterface
     */
    private function queueFor(EventInterface $event): ListenerQueueInterface
    {
        $this->queue->flush();
        $this->queue->queueListeners(
          $this->queuedListenersFor($event)
        );

        return $this->queue;
    }

    /**
     * @param EventInterface $event
     *
     * @return mixed
     */
    private function queuedListenersFor(EventInterface $event)
    {
        yield from $this->mappedListeners->filter(
            $this->filter->filter($event)
        );
    }
}
