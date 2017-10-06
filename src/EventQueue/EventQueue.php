<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter\EventQueue;

use Ds\PriorityQueue;
use EventIO\InterOp\EventInterface;

final class EventQueue implements EventQueueInterface
{
    /**
     * @var EventQueue
     */
    private $queue;

    /**
     * @param iterable $events Events to queue
     *
     * @return EventQueue
     */
    public static function create(iterable $events = null)
    {
        $queue = new PriorityQueue();

        $eventQueue = new self($queue);
        if ($events) {
            foreach ($events as $event) {
                $eventQueue->queue($event);
            }
        }

        return $eventQueue;
    }

    /**
     * EventQueue constructor.
     *
     * @param \Ds\PriorityQueue $queue A Queue to put events into
     */
    private function __construct(PriorityQueue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * @param EventInterface[] ...$events
     *
     * @return EventQueueInterface
     */
    public function queueEvent(...$events): EventQueueInterface
    {
        return $this->queueEvents($events);
    }

    /**
     * @param iterable $events
     *
     * @return EventQueueInterface
     */
    public function queueEvents(iterable $events): EventQueueInterface
    {
        foreach ($events as $event) {
            $this->queue->push($event);
        }

        return $this;
    }

    /**
     * @return iterable
     */
    public function events(): iterable
    {
        while (!$this->queue->isEmpty()) {
            yield $this->queue->pop();
        }
    }
}
