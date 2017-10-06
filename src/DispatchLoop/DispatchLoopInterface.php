<?php

namespace Bounce\Emitter\DispatchLoop;

interface DispatchLoopInterface
{
    public function __invoke();

    /**
     * @return bool
     */
    public function isDispatching(): bool;


}
