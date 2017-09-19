<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter\Collection;

use Bounce\Bounce\MappedListener\Queue\ListenerQueueInterface;
use Bounce\Emitter\MappedListener\Filter\EventListeners;
use Bounce\Emitter\MappedListener\MappedListenerInterface;
use Ds\Set;
use EventIO\InterOp\EventInterface;

/**
 * Class MappedListeners
 * @package Bounce\Bounce\MappedListener\Collection
 */
class MappedListeners implements MappedListenerCollectionInterface
{
    /**
     * @var \Ds\Set
     */
    private $mappedListeners;

    /**
     * @var \Bounce\Bounce\MappedListener\Queue\ListenerQueueInterface
     */
    private $queue;

    private $filter;

    /**
     * @param \Bounce\Bounce\MappedListener\Queue\ListenerQueueInterface $queue
     * @param                                                    $filter
     *
     * @return \Bounce\Bounce\MappedListener\Collection\MappedListeners
     */
    public static function create(ListenerQueueInterface $queue, $filter): self
    {
        return new self($queue, $filter);
    }

    /**
     * MappedListeners constructor.
     *
     * @param \Bounce\Bounce\MappedListener\Queue\ListenerQueueInterface $queue
     * @param                                                    $filter
     *
     * @internal param iterable $mappedListeners
     */
    private function __construct(ListenerQueueInterface $queue, $filter)
    {
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
     * @return \Bounce\Bounce\MappedListener\Queue\PriorityListenerQueue
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
     * @return mixed
     */
    private function queuedListenersFor(EventInterface $event)
    {
        yield from $this->mappedListeners->filter(
            $this->filter->filter($event)
        );
    }


}
