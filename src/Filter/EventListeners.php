<?php

namespace Bounce\Emitter\Filter;

use Bounce\Emitter\MappedListener\MappedListenerInterface;

/**
 * Class EventListeners.
 */
class EventListeners
{
    private $event;

    /**
     * @param \Bounce\Emitter\MappedListener\MappedListenerInterface $mappedListener
     *
     * @return bool
     */
    public function __invoke(MappedListenerInterface $mappedListener)
    {
        return $mappedListener->matches($this->event);
    }

    /**
     * @param $event
     *
     * @return $this
     */
    public function filter($event)
    {
        $this->event = $event;

        return $this;
    }
}
