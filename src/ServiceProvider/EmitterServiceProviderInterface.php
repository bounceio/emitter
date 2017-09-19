<?php

namespace Bounce\Emitter\ServiceProvider;

/**
 * Interface EmitterServiceProviderInterface
 */
interface EmitterServiceProviderInterface
{
    const EMITTER                    = 'bounce.emitter';
    const ACCEPTOR                   = 'bounce.acceptor';
    const ACCEPTOR_MIDDLEWARE        = self::ACCEPTOR.'.middleware';
    const DISPATCHER                 = 'bounce.dispatcher';
    const DISPATCHER_MIDDLEWARE      = self::DISPATCHER.'.middleware';
    const MAPPED_LISTENER_COLLECTION = 'bounce.collection.mapped_listeners';
}
