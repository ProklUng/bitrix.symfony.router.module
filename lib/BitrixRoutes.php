<?php

namespace Proklung\Symfony\Router;

use Symfony\Component\Routing\RouterInterface;

/**
 * Class BitrixRoutes
 * @package Proklung\Symfony\Router
 *
 * @since 26.07.2021
 */
class BitrixRoutes extends BaseRoutesConfigurator
{
    /**
     * @var RouterInterface $router Роутер.
     */
    protected static $router;
}