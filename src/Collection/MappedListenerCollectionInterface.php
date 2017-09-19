<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter\Collection;

use Bounce\Emitter\MappedListener\MappedListenerInterface;
use EventIO\InterOp\EventInterface;

/**
 * Interface MappedListenerCollectionInterface.
 */
interface MappedListenerCollectionInterface
{
    /**
     * @param MappedListenerInterface[] ...$mappedListener
     *
     * @return mixed
     */
    public function add(MappedListenerInterface ...$mappedListener);

    /**
     * @param EventInterface $event
     *
     * @return mixed
     */
    public function listenersFor(EventInterface $event): iterable;

    public function addListeners(iterable $mappedListeners);
}
