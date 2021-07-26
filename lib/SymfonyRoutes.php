<?php

namespace Proklung\Symfony\Router;

use Symfony\Component\Routing\RouterInterface;

/**
 * Class SymfonyRoutes
 * @package Proklung\Symfony\Router
 *
 * @since 26.07.2021
 */
class SymfonyRoutes extends BaseRoutesConfigurator
{
    /**
     * @var RouterInterface $router Роутер.
     */
    protected static $router;
}