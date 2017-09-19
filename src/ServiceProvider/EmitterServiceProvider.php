<?php

namespace Bounce\Emitter\ServiceProvider;

use Bounce\Emitter\Acceptor\Acceptor;
use Bounce\Emitter\Collection\MappedListeners;
use Bounce\Emitter\Dispatcher\Dispatcher;
use Bounce\Emitter\Emitter;
use Bounce\Emitter\ListenerQueue\DsListenerQueue;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class EmitterServiceProvider implements ServiceProviderInterface
{
    const EMITTER                       = 'bounce.emitter';
    const ACCEPTOR                      = 'bounce.acceptor';
    const ACCEPTOR_MIDDLEWARE           = self::ACCEPTOR.'.middleware';
    const DISPATCHER                    = 'bounce.dispatcher';
    const DISPATCHER_MIDDLEWARE         = self::DISPATCHER.'.middleware';

    const MAPPED_LISTENER_COLLECTION    = 'bounce.collection.mapped_listeners';

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['bounce.mapped_listeners.filter'] = function () {
            return new EventListeners();
        };

        $pimple['bounce.mapped_listeners.queue'] = function () {
            return new DsListenerQueue();
        };

        $pimple[self::MAPPED_LISTENER_COLLECTION] = function (Container $con) {
            return MappedListeners::create(
                $con['bounce.mapped_listeners.queue'],
                $con['bounce.mapped_listeners.filter']
            );
        };

        $pimple[self::ACCEPTOR] = function (Container $con) {
            return Acceptor::create(
                $con[self::ACCEPTOR_MIDDLEWARE],
                $con[self::MAPPED_LISTENER_COLLECTION]
            );
        };

        $pimple[self::DISPATCHER] = function (Container $con) {
            return Dispatcher::create(
                $con[self::DISPATCHER_MIDDLEWARE]
            );
        };

        $pimple[self::EMITTER] = function (Container $con) {
            return new Emitter(
                $con[self::ACCEPTOR],
                $con[self::DISPATCHER]
            );
        };
    }
}
