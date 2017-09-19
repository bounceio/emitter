<?php
namespace Bounce\Emitter\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class EmitterServiceProvider implements ServiceProviderInterface
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
        // TODO: Implement register() method.
    }
}