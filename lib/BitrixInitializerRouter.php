<?php

namespace Proklung\Symfony\Router;

use Proklung\Symfony\Router\Contracts\RouterInitializerInterface;

/**
 * Class BitrixInitializerRouter
 * @package Proklung\Symfony\Router
 *
 * @since 24.07.2021
 */
class BitrixInitializerRouter implements RouterInitializerInterface
{
    /**
     * @inheritDoc
     */
    public function init(InitRouter $router)
    {
        AddEventHandler('main', 'OnProlog', [$router, 'handle']);
    }
}