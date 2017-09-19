<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter\Middleware;

use Bounce\Emitter\MappedListener\MappedListenerInterface;

/**
 * Interface AcceptorMiddlewareInterface.
 */
interface AcceptorMiddlewareInterface
{
    /**
     * @param $map
     * @param $listener
     * @param $priority
     *
     * @return MappedListenerInterface
     */
    public function listenerAdd($map, $listener, $priority): MappedListenerInterface;
}
