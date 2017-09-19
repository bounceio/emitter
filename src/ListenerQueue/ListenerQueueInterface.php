<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter\ListenerQueue;

use Bounce\Emitter\MappedListener\MappedListenerInterface;

/**
 * Interface ListenerQueueInterface.
 */
interface ListenerQueueInterface
{
    /**
     * @param MappedListenerInterface[] ...$mappedListeners
     *
     * @return mixed
     */
    public function queue(MappedListenerInterface ...$mappedListeners);

    /**
     * @return iterable
     */
    public function listeners(): iterable;
}
