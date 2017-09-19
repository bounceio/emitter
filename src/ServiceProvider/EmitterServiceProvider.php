<?php

namespace Bounce\Emitter\ServiceProvider;

use Bounce\Emitter\Acceptor\Acceptor;
use Bounce\Emitter\Collection\MappedListeners;
use Bounce\Emitter\Dispatcher\Dispatcher;
use Bounce\Emitter\Emitter;
use Bounce\Emitter\Filter\EventListeners;
use Bounce\Emitter\ListenerQueue\DsListenerQueue;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class EmitterServiceProvider implements
    ServiceProviderInterface,
    EmitterServiceProviderInterface
{
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
        $pimple['bounce.mapped_listeners.filter'] = function (): EventListeners {
            return new EventListeners();
        };

        $pimple['bounce.mapped_listeners.queue'] = function (): DsListenerQueue {
            return new DsListenerQueue();
        };

        $pimple[self::MAPPED_LISTENER_COLLECTION] = function (Container $con) {
            return MappedListeners::create(
                $con['bounce.mapped_listeners.filter'],
                $con['bounce.mapped_listeners.queue']
            );
        };

        $pimple[self::ACCEPTOR] = function (Container $con): Acceptor {
            return Acceptor::create(
                $con[self::ACCEPTOR_MIDDLEWARE],
                $con[self::MAPPED_LISTENER_COLLECTION]
            );
        };

        $pimple[self::DISPATCHER] = function (Container $con): Dispatcher {
            return Dispatcher::create(
                $con[self::DISPATCHER_MIDDLEWARE]
            );
        };

        $pimple[self::EMITTER] = function (Container $con): Emitter {
            return new Emitter(
                $con[self::ACCEPTOR],
                $con[self::DISPATCHER]
            );
        };
    }
}
