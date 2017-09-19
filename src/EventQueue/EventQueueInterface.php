<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */
namespace Bounce\Emitter\EventQueue;

use EventIO\InterOp\EventInterface;

/**
 * Interface EventQueueInterface
 * @package Bounce\Bounce\EventQueue
 */
interface EventQueueInterface
{
    /**
     * @param EventInterface[] ...$events
     * @return EventQueueInterface
     */
    public function queueEvent(...$events): EventQueueInterface;

    /**
     * @param iterable $events
     *
     * @return EventQueueInterface
     */
    public function queueEvents(iterable $events): EventQueueInterface;

    /**
     * @return iterable
     */
    public function events(): iterable;
}
