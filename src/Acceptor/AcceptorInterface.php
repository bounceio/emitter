<?php
/**
 * @author       Barney Hanlon <barney@shrikeh.net>
 * @copyright    Barney Hanlon 2017
 * @license      https://opensource.org/licenses/MIT
 */

namespace Bounce\Emitter\Acceptor;

use EventIO\InterOp\EventInterface;
use EventIO\InterOp\ListenerAcceptorInterface;

/**
 * Interface AcceptorInterface.
 */
interface AcceptorInterface extends ListenerAcceptorInterface
{
    /**
     * @param \EventIO\InterOp\EventInterface $event
     *
     * @return iterable
     */
    public function __invoke(EventInterface $event): iterable;

    /**
     * @param EventInterface $event
     *
     * @return iterable
     */
    public function listenersFor(EventInterface $event): iterable;
}
