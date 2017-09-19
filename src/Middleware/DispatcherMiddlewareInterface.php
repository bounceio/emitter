<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter\Middleware;

/**
 * Interface AcceptorMiddlewareInterface.
 */
interface DispatcherMiddlewareInterface
{
    /**
     * @param $eventDispatchLoop
     *
     * @return mixed
     */
    public function dispatch($eventDispatchLoop);
}
