<?php

namespace App\Dto\Messenger;

/**
 * Class PredefinedRoutesMessage
 * @package App\Dto\Messenger
 */
class PredefinedRoutesMessage implements MessengerMessageInterface
{
    /**
     * routes.
     *
     * @var array
     */
    private array $routes;

    /**
     * PredefinedRoutesMessage.
     *
     * @param array $routes
     *
     * @return void
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @return array
     */
    public function getRoutes() : array
    {
        return $this->routes;
    }
}
