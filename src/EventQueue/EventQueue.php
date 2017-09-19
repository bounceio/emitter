<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter\EventQueue;

use Ds\Queue;
use EventIO\InterOp\EventInterface;

final class EventQueue implements EventQueueInterface
{
    /**
     * @var EventQueue
     */
    private $queue;

    /**
     * @param iterable $events Events to queue
     * @return EventQueue
     */
    public static function create(iterable $events = null)
    {
        $queue = new Queue();

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
     * @param \Ds\Queue $queue A Queue to put events into
     */
    private function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * @param EventInterface[] ...$events
     * @return EventQueueInterface
     */
    public function queueEvent( ...$events): EventQueueInterface
    {
        return $this->queueEvents($events);
    }

    /**
     * @param iterable $events
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
