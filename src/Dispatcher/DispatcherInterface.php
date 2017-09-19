<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter\Dispatcher;

use Bounce\Emitter\Acceptor\AcceptorInterface;

interface DispatcherInterface
{
    /**
     * @param array ...$events
     *
     * @return DispatcherInterface
     */
    public function enqueue(...$events): DispatcherInterface;

    /**
     * @return bool
     */
    public function isDispatching(): bool;

    /**
     * @param AcceptorInterface $acceptor
     * @param iterable          $events
     *
     * @return DispatcherInterface
     */
    public function dispatch(
        AcceptorInterface $acceptor,
        iterable $events = []
    ): DispatcherInterface;
}
