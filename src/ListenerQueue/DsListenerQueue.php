<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter\ListenerQueue;

use Bounce\Emitter\MappedListener\MappedListenerInterface;
use Ds\PriorityQueue as DsPriorityQueue;
use Ds\Set;

/**
 * Class DsListenerQueue
 * @package Bounce\Bounce\MappedListener\Queue
 */
class DsListenerQueue implements ListenerQueueInterface
{
    /**
     * @var \Ds\PriorityQueue
     */
    private $prioritizedQueue;

    /**
     * DsListenerQueue constructor.
     * @param iterable $mappedListeners
     */
    public function __construct(iterable $mappedListeners = [])
    {
        $this->prioritizedQueue = new DsPriorityQueue();

        $this->queueListeners($mappedListeners);
    }

    /**
     * @return \Bounce\Emitter\ListenerQueue\ListenerQueueInterface
     */
    public function flush(): ListenerQueueInterface
    {
        $this->prioritizedQueue->clear();

        return $this;
    }

    /**
     * @param MappedListenerInterface[] ...$mappedListeners
     * @return mixed
     */
    public function queue(MappedListenerInterface ...$mappedListeners)
    {
        return $this->queueListeners($mappedListeners);
    }

    /**
     * @param iterable $mappedListeners
     * @return $this
     */
    public function queueListeners(iterable $mappedListeners)
    {
        $mappedListeners = new Set($mappedListeners);

        foreach ($mappedListeners as $mappedListener) {
            $this->addListener($mappedListener);
        }

        return $this;
    }

    /**
     * @return iterable
     */
    public function listeners(): iterable
    {
        while (!$this->prioritizedQueue->isEmpty()) {
            yield $this->prioritizedQueue->pop()->listener();
        }
    }

    /**
     * @param \Bounce\Emitter\MappedListener\MappedListenerInterface $mappedListener
     */
    private function addListener(MappedListenerInterface $mappedListener)
    {
        $this->prioritizedQueue->push(
            $mappedListener,
            $mappedListener->priority()
        );
    }
}
