<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter\MappedListener;

/**
 * Interface MappedListenerInterface.
 */
interface MappedListenerInterface
{
    /**
     * @param $event
     *
     * @return bool
     */
    public function matches($event): bool;

    /**
     * @return callable|\EventIO\InterOp\ListenerInterface;
     */
    public function listener();

    /**
     * @return mixed
     */
    public function priority();
}
