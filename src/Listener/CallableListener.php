<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter\Listener;

use EventIO\InterOp\EventInterface;
use EventIO\InterOp\ListenerInterface;

/**
 * Class CallableListener.
 */
class CallableListener implements ListenerInterface
{
    /**
     * @var callable
     */
    private $listener;

    /**
     * CallableListener constructor.
     *
     * @param callable $listener a lambda or invokable object to handle the event
     */
    public function __construct(callable $listener)
    {
        $this->listener = $listener;
    }

    /**
     * Handle an event.
     *
     * @param EventInterface $event The event being emitted
     */
    public function handle(EventInterface $event)
    {
        $listener = $this->listener;

        $listener($event);
    }
}
